<?php

declare(strict_types=1);

/*
 * What phil/dev-tools reads of this application: its paths and its exceptions to the shared configurations.
 * See vendor/phil/dev-tools/doc/configuration.md for every section.
 */
return [
    'paths' => ['src', 'tests', 'migrations', 'bin', 'public', 'importmap.php'],
    // The API tests share one SQLite file: they run one after the other, with PHPUnit.
    'phpunit' => ['parallel' => false],
    // Besides the Symfony components, the classes of src/ may use these libraries and bundles, and no other: a new one
    // is added here on purpose, after it is required by composer.json.
    'deptrac' => ['allow' => [
        '^ApiPlatform\\\\Metadata\\\\.*',
        '^Doctrine\\\\(Bundle\\\\DoctrineBundle|DBAL|ORM|Persistence)\\\\.*',
        '^Symfony\\\\Bridge\\\\Doctrine\\\\.*',
        '^Symfony\\\\Bundle\\\\FrameworkBundle\\\\.*',
        '^Twig\\\\.*',
    ]],
    // The Stimulus controllers and the scripts of the application; assets/vendor holds the packages of the importmap.
    'js' => ['paths' => ['assets/controllers', 'assets/app.js', 'assets/bootstrap.js']],
    // Used through the configuration, the templates, the front controller or the console, never by a class of src/;
    // phil/api-contract and phil/idempotency are required for their stability flag, phil/api-contract-bundle needs them;
    // the migrations use phil/idempotency.
    'unused' => ['ignore' => [
        'ext-iconv', 'ext-simplexml', 'api-platform/doctrine-orm', 'api-platform/openapi', 'api-platform/symfony', 'doctrine/doctrine-migrations-bundle',
        'doctrine/migrations', 'league/commonmark', 'nelmio/cors-bundle', 'phil/api-contract',
        'phil/api-contract-bundle',
        'phil/api-keys-bundle', 'phil/idempotency', 'sentry/sentry-symfony', 'symfony/asset', 'symfony/asset-mapper',
        'symfony/dotenv', 'symfony/flex', 'symfony/intl',
        'symfony/monolog-bundle', 'symfony/property-access', 'symfony/runtime',
        'symfony/stimulus-bundle', 'symfony/translation',
        'symfony/twig-bundle', 'symfony/ux-turbo', 'symfony/web-link', 'twig/extra-bundle',
        'twig/markdown-extra',
    ]],
];
