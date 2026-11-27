<?php

// The tasks of the application: run `castor` to list them. Tested with Castor 1.9.
// https://castor.jolicode.com
//
// The tests, the lints, the fixers, the continuous integration and the git hooks are the common tasks of
// phil/dev-tools, imported below and set by PHIL_CASTOR (see vendor/phil/dev-tools/doc/castor.md). This file keeps the
// tasks of this application only: its servers, its database, its API tokens and its own checks.

declare(strict_types=1);

// Every task runs from the directory of the project, whichever directory castor is called from.
defined('CASTOR_USE_CHDIR') || define('CASTOR_USE_CHDIR', true);

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\exit_code;
use function Castor\import;
use function Castor\io;
use function Phil\DevTools\Castor\aborted;
use function Phil\DevTools\Castor\aggregate;
use function Phil\DevTools\Castor\success;
use function Phil\DevTools\Castor\title;

const PHIL_CASTOR = [
    // E2E is left out: it needs Chrome and chromedriver, and `castor test:e2e` runs it alone, outside `castor test`,
    // `castor coverage`, `castor ci` and the continuous integration.
    'suites' => ['Unit', 'Integration', 'Api', 'Functional'],
    'coverage_threshold' => 100,
    'lint_also' => ['lint_doctrine', 'lint_openapi'],
    'ci_also' => ['test_schema'],
];

import(__DIR__.'/vendor/phil/dev-tools/resources/castor/tasks.php');

// Servers ————————————————————————————————————————————————————————————————————

#[AsTask(namespace: 'symfony', description: 'Migrate the SQLite database, then serve the application with the Symfony binary', aliases: ['start'])]
function start(): int
{
    title('symfony:start');
    $exitCode = exit_code('bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration');
    if ($exitCode !== 0) {
        aborted('The database could not be migrated: the web server has NOT been started.');

        return $exitCode;
    }

    return exit_code('symfony serve --daemon');
}

#[AsTask(namespace: 'symfony', description: 'Stop the web server of the Symfony binary', aliases: ['stop'])]
function stop(): int
{
    title('symfony:stop');

    return exit_code('symfony server:stop');
}

#[AsTask(namespace: 'docker', description: 'Start FrankenPHP in worker mode and PostgreSQL with Docker Compose, then migrate', aliases: ['up'])]
function up(): int
{
    title('docker:up');
    $exitCode = exit_code('docker compose up --detach --wait --build');
    if ($exitCode !== 0) {
        return success($exitCode);
    }

    return success(exit_code('docker compose exec php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration'));
}

#[AsTask(namespace: 'docker', description: 'Stop the Docker Compose services', aliases: ['down'])]
function down(): int
{
    title('docker:down');

    return success(exit_code('docker compose down --remove-orphans'));
}

#[AsTask(namespace: 'symfony', description: 'Switch to the production environment', aliases: ['go-prod'])]
function go_prod(): int
{
    title('symfony:go_prod');
    if (io()->confirm('Are you sure you want to switch to the production environment? This will overwrite your .env.local file.', false)) {
        return success(aggregate(exit_code('cp .env.local.dist .env.local'), exit_code('bin/console asset-map:compile')));
    }

    aborted();

    return 0;
}

#[AsTask(namespace: 'symfony', description: 'Switch to the development environment', aliases: ['go-dev'])]
function go_dev(): int
{
    title('symfony:go_dev');
    if (io()->confirm('Are you sure you want to switch to the development environment? This will delete your .env.local file.', false)) {
        return success(aggregate(exit_code('rm -f .env.local'), exit_code('rm -rf ./public/assets/*')));
    }

    aborted();

    return 0;
}

#[AsTask(namespace: 'symfony', description: 'Purge all Symfony cache and logs', aliases: ['purge'])]
function purge(): int
{
    title('symfony:purge');

    return success(exit_code('rm -rf ./var/cache/* ./var/log/* ./var/coverage/*'));
}

// Database and API tokens ————————————————————————————————————————————————————

#[AsTask(name: 'migrate', namespace: 'db', description: 'Run the migrations on the database of DATABASE_URL', aliases: ['migrate'])]
function db_migrate(): int
{
    title('db:migrate');

    return success(exit_code('bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration'));
}

#[AsTask(name: 'token', namespace: 'api', description: 'Create an API token for the example client, with the scope books:write', aliases: ['token'])]
function api_token(#[AsOption(description: 'The owner of the token')] string $owner = 'example-client'): int
{
    title('api:token');

    return success(exit_code(sprintf('bin/console phil:api-keys:create %s %s --scope=books:write', escapeshellarg($owner), escapeshellarg('Created by castor api:token'))));
}

#[AsTask(name: 'seed', namespace: 'db', description: 'Add example books to an empty database and create an API token for the example client', aliases: ['seed'])]
function db_seed(#[AsOption(description: 'When the token expires, such as "+30 days"')] ?string $expires = null): int
{
    title('db:seed');
    $command = 'bin/console app:example:seed';
    if ($expires !== null) {
        $command .= ' --expires='.escapeshellarg($expires);
    }

    return success(exit_code($command));
}

// The checks of this application, which lint:all and ci:all call through PHIL_CASTOR ———————————————————————————

/** The SQLite database on which test:schema runs the migrations, removed afterwards. */
const SCHEMA_DATABASE = 'var/data_schema.db';

#[AsTask(name: 'schema', namespace: 'test', description: 'Run the migrations on a fresh SQLite database, then check that its schema matches the Doctrine mapping', aliases: ['test-schema'])]
function test_schema(): int
{
    title('test:schema');
    $context = context()->withEnvironment(['DATABASE_URL' => 'sqlite:///%kernel.project_dir%/'.SCHEMA_DATABASE]);
    exit_code('rm -f ./'.SCHEMA_DATABASE);
    io()->note('Running the migrations on '.SCHEMA_DATABASE.'...');
    $exitCode = exit_code('bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration', context: $context);
    if ($exitCode === 0) {
        io()->note('Comparing the schema with the mapping...');
        $exitCode = exit_code('bin/console doctrine:schema:validate', context: $context);
    }

    exit_code('rm -f ./'.SCHEMA_DATABASE);

    return success($exitCode);
}

#[AsTask(name: 'doctrine', namespace: 'lint', description: 'Check the Doctrine mapping', aliases: ['lint-doctrine'])]
function lint_doctrine(): int
{
    title('lint:doctrine');

    return success(exit_code('bin/console doctrine:schema:validate --skip-sync'));
}

#[AsTask(name: 'openapi', namespace: 'lint', description: 'Export the OpenAPI document and check it against the contract of an unversioned REST API', aliases: ['lint-openapi'])]
function lint_openapi(): int
{
    title('lint:openapi');

    $exitCode = exit_code('bin/console api:openapi:export --output=var/openapi.json');
    if ($exitCode !== 0) {
        return success($exitCode);
    }

    return success(exit_code('bin/console phil:api-contract:verify var/openapi.json --level=recommended --fail-on=warning'));
}

// Helpers ————————————————————————————————————————————————————————————————————

#[AsTask(name: 'check-requirements', namespace: 'helpers', description: 'Check the requirements of Symfony', aliases: ['check-requirements'])]
function check_requirements(): int
{
    title('helpers:check-requirements');

    return success(exit_code('vendor/bin/requirements-checker'));
}
