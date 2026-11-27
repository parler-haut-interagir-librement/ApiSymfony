# ApiSymfony 🕸

[![Latest Version](https://img.shields.io/packagist/v/phil/apisymfony.svg?style=flat-square)](https://packagist.org/packages/phil/apisymfony)
[![PHP Version Require](https://poser.pugx.org/phil/apisymfony/require/php?style=flat-square)](https://packagist.org/packages/phil/apisymfony)
[![Symfony Version](https://img.shields.io/badge/Symfony-8.2-black?logo=symfony)](https://symfony.com/8)
[![API Platform](https://img.shields.io/badge/API%20Platform-5-38a9b4)](https://api-platform.com)
[![PHPStan level](https://img.shields.io/badge/PHPStan-level%20max-success?style=flat-square)](phpstan.neon)
[![Software License](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status (GitHub)](https://img.shields.io/github/actions/workflow/status/parler-haut-interagir-librement/ApiSymfony/symfony.yml?branch=main&style=flat-square)](https://github.com/parler-haut-interagir-librement/ApiSymfony/actions?query=workflow%3ASymfony+branch%3Amain)

## About 🖋

ApiSymfony is a [Symfony 8.2](https://symfony.com/8) application template for a REST API, ready to use: an
[API Platform](https://api-platform.com) application with an example resource, API tokens with scopes, errors as
problem details, an OpenAPI document checked against the contract of an unversioned API, tests of every kind and
strict quality gates. Start an API from it, delete the example, and keep the rest.

It derives from [MicroSymfony](https://github.com/parler-haut-interagir-librement/MicroSymfony), the PHIL version of
[strangebuzz/MicroSymfony](https://github.com/strangebuzz/MicroSymfony), created and maintained by
[COil](https://github.com/COil) of [Strangebuzz](https://www.strangebuzz.com): the same tasks, the same tests, the
same gates and the same identity, with API Platform and a database instead of the examples of pages. What differs, and
why, is listed in [Differences from MicroSymfony](#differences-from-microsymfony-).

## Table of Contents 📖

* [Demo](#demo-)
* [Quick-start](#quick-start-)
  * [With the Symfony binary](#with-the-symfony-binary-)
  * [With FrankenPHP and PostgreSQL](#with-frankenphp-and-postgresql-)
  * [With MySQL](#with-mysql-)
* [Requirements](#requirements-)
* [Stack](#stack-)
* [The API](#the-api-)
* [Tasks](#tasks-)
* [Quality gates](#quality-gates-)
* [Differences from MicroSymfony](#differences-from-microsymfony-)
* [Notes](#notes-)
* [What it doesn't ship?](#what-it-doesnt-ship-)
* [PHIL bundles](#phil-bundles-)
* [References](#references-)
* [Others “Proclaim Honestly, Interact Liberally” Skeletons](#others-proclaim-honestly-interact-liberally-skeletons-)
* [Contributing](#contributing-)
* [Security](#security-)
* [Credits](#credits-)
* [License](#license-)
* [Built with ApiSymfony](#built-with-apisymfony-)

## Demo 🌈

A demo of ApiSymfony runs in the browser on [ph-il.ca](https://ph-il.ca), with the other templates of PHIL: PHP
compiled to WebAssembly, with the code of the template embedded. Its demo page shows an example API token, and the API
starts with a few books: both are created when the demo is built, by `bin/console app:example:seed` on the embedded
copy only, so no installed application holds them. The database lives in the memory of the page and starts again from
these books at each reload.

## Quick-start ⚡

Create a new project from the latest release:

    composer create-project phil/apisymfony my-api && cd my-api

The repository is also a GitHub template repository: the "Use this template" button creates a new repository from the
main branch, whose commits all pass the continuous integration.

### With the Symfony binary 🎶

You need the [Symfony binary](https://symfony.com/download#step-1-install-symfony-cli) and
[Castor](https://castor.jolicode.com/getting-started/installation/). The database is a SQLite file in `var/`, migrated
before the server starts:

    castor start

Open [https://127.0.0.1:8000/api/docs](https://127.0.0.1:8000/api/docs) (if port 8000 is free) for the Swagger UI of
the API, or [https://127.0.0.1:8000](https://127.0.0.1:8000) for this README. `castor stop` stops the server.

### With FrankenPHP and PostgreSQL 🧟

You need [Docker](https://www.docker.com/). `compose.yaml` serves the application with
[FrankenPHP](https://frankenphp.dev/) in worker mode, which boots the kernel once and serves request after request,
and restarts its workers when a file changes; the database is PostgreSQL 17, migrated once the services are up:

    castor up

Open [https://localhost/api/docs](https://localhost/api/docs). `castor down` stops the services. The image of
`docker/Dockerfile` is for development: an API deployed from this template brings its own deployment.

### With MySQL 🐬

MySQL is the other database the template supports. In `compose.yaml`, replace the `database` service with a
`mysql:8.4` image and set `DATABASE_URL` to:

    mysql://app:app@database:3306/app?serverVersion=8.4&charset=utf8mb4

With the Symfony binary, set the same variable in `.env.local`, with the host of your server. The migration describes
its tables through the schema API of Doctrine DBAL and is written in the SQL of the database it runs on: it runs on
SQLite, PostgreSQL and MySQL without a change.

## Requirements ⚙

* [PHP 8.5](https://www.php.net/releases/8.5/en.php) or later, with the extensions of `composer.json`, and
  [Castor](https://castor.jolicode.com)
* The [Symfony binary](https://symfony.com/download), or [Docker](https://www.docker.com)
* The [Xdebug](https://xdebug.org/) or [PCOV](https://github.com/krakjoe/pcov) extension for the code coverage report

## Stack 🔗

* [PHP 8.5](https://www.php.net/releases/8.5/en.php)
* [Symfony 8.2](https://symfony.com/8), from its release candidate, with [Symfony Security](https://symfony.com/doc/current/security.html)
* [API Platform 5](https://api-platform.com), REST only
* [Doctrine ORM 3 (DBAL 4)](https://www.doctrine-project.org) and its migrations, on SQLite, PostgreSQL or MySQL
* [`phil/api-keys-bundle`](https://packagist.org/packages/phil/api-keys-bundle) and
  [`phil/api-contract-bundle`](https://packagist.org/packages/phil/api-contract-bundle)
* [`phil/cors-bundle`](https://packagist.org/packages/phil/cors-bundle) for the cross-origin requests
* [Twig 3](https://twig.symfony.com) and [Pico CSS 2](https://picocss.com) for the home page, in English and in French
* [Sentry](https://docs.sentry.io/platforms/php/guides/symfony/) for the errors of the production environment
* [PHPUnit 13](https://phpunit.de) and [Foundry](https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html)
* [FrankenPHP](https://frankenphp.dev/) in worker mode, through Docker Compose

## The API 🔌

### The resources

API Platform turns each class marked `#[ApiResource]` into REST operations under `/api`. The example is
[Book](src/Entity/Book.php), a Doctrine entity:

| Operation | Who |
|---|---|
| `GET /api/books`, `GET /api/books/{id}` | Everyone |
| `POST /api/books`, `PATCH /api/books/{id}`, `DELETE /api/books/{id}` | A token with the scope `books:write` |

The resources are written in JSON-LD (`application/ld+json`) or in JSON (`application/json`), as the `Accept` header
asks; a change is sent as `application/merge-patch+json`. The collections are paginated, 30 items a page. API
Platform is configured for REST only: no GraphQL. The configuration is in
[config/packages/api_platform.yaml](config/packages/api_platform.yaml).

### The tokens

The API is stateless: a request carries a token of [`phil/api-keys-bundle`](https://packagist.org/packages/phil/api-keys-bundle)
in its `Authorization` header, checked by the `access_token` authenticator of Symfony. A token is shown once, when it
is created; the database keeps only its keyed hash. It holds scopes, expires, and can be revoked or rotated:

    castor token
    curl -X POST https://127.0.0.1:8000/api/books \
         -H "Authorization: Bearer apisf_…" -H "Content-Type: application/ld+json" \
         -d '{"title": "Kamouraska", "author": "Anne Hébert"}'

`castor seed` adds a few example books to an empty database and creates such a token, printed alone on the last
line: `bin/console app:example:seed`, with `--expires` for a lifetime other than the default one.

`castor token` creates a token for `example-client`, the client [config/packages/security.yaml](config/packages/security.yaml)
keeps in memory, with the scope `books:write`. The scopes are declared in
[config/packages/phil_api_keys.yaml](config/packages/phil_api_keys.yaml), and each operation that writes names the one
it requires in its `security` expression: `is_granted('API_SCOPE', 'books:write')`. `bin/console phil:api-keys:list`,
`phil:api-keys:revoke` and `phil:api-keys:purge-expired` manage the tokens.

The tokens are hashed with `API_KEYS_HASH_KEY`, 32 random bytes or more. `.env.dev` and `.env.test` hold keys known to
everyone, for the workstation and the tests; `.env` leaves it empty, so that no other environment starts with a known
key. Set it as a secret:

    php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;' | bin/console secrets:set API_KEYS_HASH_KEY -

### The errors

Every error of the API is a problem (RFC 9457), `application/problem+json`: API Platform writes those of its
operations, with the violations of the constraints for a `422`; `phil/api-contract-bundle` writes the `401` of a
missing or refused token, with its `WWW-Authenticate` challenge, and every `ProblemException` a controller throws; a
`403` names no reason. Symfony writes the others as problems too for a client that accepts JSON, such as the `404` of
[SlugifyAction](src/Controller/SlugifyAction.php), an endpoint beside the resources.

### The OpenAPI document

API Platform publishes the OpenAPI document of the API at `/api/docs.jsonopenapi`, and its Swagger UI at `/api/docs`,
with an "Authorize" button for the bearer token. `castor lint:openapi` exports it to `var/openapi.json` and checks it
against the rules of [`phil/api-contract`](https://packagist.org/packages/phil/api-contract) for an unversioned REST
and JSON API: no version in a path, JSON media types, problem details for the errors, and more. It fails on a warning;
[OpenApiContractTest](tests/Integration/Api/OpenApiContractTest.php) runs the same check with the tests.

### Cross-origin requests

[`phil/cors-bundle`](https://packagist.org/packages/phil/cors-bundle) answers the cross-origin requests on `/api`, as
[config/packages/phil_cors.yaml](config/packages/phil_cors.yaml) sets them:

* the origins that `CORS_ALLOW_ORIGIN` matches, a regular expression anchored by `^` and `$`: `localhost` and
  `127.0.0.1`, on any port, in `.env`; set the origins of your front ends in `.env.local` or in the environment of the
  server;
* the methods `GET`, `POST`, `PATCH` and `DELETE`, the request headers `Content-Type` and `Authorization`, the
  response header `Link` readable by the page, preflights kept an hour by the browser;
* no credentials: the API reads a bearer token, never a cookie, so no origin may send one.

The bundle answers the preflights before the router and the firewall, so no `OPTIONS` route is needed, and adds
`Vary: Origin` to every answer of `/api`. Another origin gets no `Access-Control-Allow-Origin`, which the browser reads
as a refusal; the pages outside `/api` send no CORS header. A wildcard origin with credentials, an unanchored pattern
or the origin `null` stops the application on its first request rather than open the API.
[CorsTest](tests/Api/CorsTest.php) checks these rules.

## Tasks 🧰

`castor` lists every task. The tests, the lints, the fixers, `ci` and `hooks` are the common tasks of
[`phil/dev-tools`](https://packagist.org/packages/phil/dev-tools), which `castor.php` imports from
`vendor/phil/dev-tools/resources/castor/tasks.php` and sets through its `PHIL_CASTOR` constant: the suites, the
coverage threshold, and the checks of this application that `lint` and `ci` call after theirs (`lint:doctrine`,
`lint:openapi`, `test:schema`). `castor.php` keeps the tasks of this application only. The main ones:

| Task | What it does |
|---|---|
| `castor start`, `castor stop` | The Symfony binary's web server, on SQLite. |
| `castor up`, `castor down` | FrankenPHP in worker mode and PostgreSQL, with Docker Compose. |
| `castor migrate` | The migrations, on the database of `DATABASE_URL`. |
| `castor token` | An API token for the example client, with the scope `books:write`. |
| `castor seed` | A few example books in an empty database, and an API token for the example client. |
| `castor test` | Every PHPUnit suite but E2E; `--filter` and `--options` pass on to PHPUnit. |
| `castor test:unit`, `test:integration`, `test:api`, `test:functional` | One suite; `castor test:suite <name>` runs any suite of `phpunit.dist.xml`. |
| `castor test:e2e` | The E2E suite in a real browser, Chrome driven by Panther: the home page on `localhost`, whose theme switcher proves that its JavaScript runs. Chrome and chromedriver must be on the `PATH`, or let `vendor/bin/bdi detect drivers` download the driver into `drivers/` after `composer require --dev dbrekelmans/bdi`. Neither `castor test`, `castor coverage`, `castor ci` nor the continuous integration runs it. |
| `castor test:next` | The tests on the next PHP version, in its Docker image. |
| `castor test:schema` | The migrations on a fresh SQLite database, then its schema compared with the Doctrine mapping. |
| `castor coverage` | The HTML coverage report in `var/coverage/`, and its threshold. |
| `castor infection` | Mutation testing. |
| `castor taint` | The taint analysis of Psalm. |
| `castor lint` | Every lint, the Doctrine mapping, the OpenAPI contract and the static analysis. |
| `castor fix` | Rector, PHP-CS-Fixer, Twig-CS-Fixer, Biome and composer-normalize. |
| `castor ci` | The continuous integration, locally: coverage, every lint, then `test:schema`. |
| `castor hooks` | The git hooks: the fast checks on the staged files before each commit. |

## Quality gates 🚦

The checks come from [`phil/dev-tools`](https://packagist.org/packages/phil/dev-tools), the shared tooling of PHIL,
adapted to an application. Each tool is installed in its own `vendor-bin/<tool>/` directory by the
[Composer bin plugin](https://github.com/bamarni/composer-bin-plugin), so that its dependencies never meet the
application's. The thin configuration files at the root (`phpstan.neon`, `rector.php`, `.php-cs-fixer.dist.php`,
`arkitect.php`) call the shared ones; `phil-dev.php` holds what is particular to the application, each exception with
its reason.

| Task | Checks |
|---|---|
| `castor lint:composer` | The dependency analysis (a class used without its package required), `composer audit`, composer-unused and composer-normalize. |
| `castor lint:php` | PHP-CS-Fixer: `@Symfony`, `@Symfony:risky`, strict types and strict comparisons. |
| `castor lint:twig` | `lint:twig`, then the code style of the templates. |
| `castor lint:js` | Biome on the Stimulus controllers. |
| `castor lint:yaml`, `lint:container` | The YAML files, `config/` and the messages included, and the container. |
| `castor lint:doctrine` | The Doctrine mapping. |
| `castor lint:openapi` | The OpenAPI document against the contract of an unversioned REST and JSON API. |
| `castor stan` (`lint:analyse`) | PHPStan at the maximum level, with the Doctrine and Symfony extensions, type coverage at 100 %, a cognitive complexity limit and forbidden calls. |
| `castor lint:rector` | Rector has nothing left to change. |
| `castor lint:architecture` | Arkitect on the shape of the classes; Deptrac on the libraries `src/` may use, listed in `phil-dev.php`. |

The functional suite holds the route smoke test of `phil/dev-tools` (`RouteSmokeTesting`): every GET route that needs
no parameter answers without a server error, the home page, `/api/slugify`, `/api/docs`, the entry point of the API and
the GET collection of each resource included.

The taint analysis of Psalm, `castor taint`, runs on its own, before a release and once a week. The `.github/`
directory also holds the security files of `phil/dev-tools`: Dependabot, with a cooldown of seven days, the OpenSSF
Scorecard and zizmor, which audits the workflows.

## Differences from MicroSymfony 🔀

| MicroSymfony | This template | Why |
|---|---|---|
| No database | Doctrine ORM and its migrations: SQLite for the Symfony binary, PostgreSQL in `compose.yaml`, MySQL documented | An API keeps its resources and its tokens. |
| A JSON endpoint in a controller | API Platform 5, REST only, beside one plain controller | The operations, the pagination, the content negotiation, the validation and the OpenAPI document come from the resource. |
| No security | A stateless firewall on `/api`, API tokens with scopes | A public API knows who calls and what for. |
| Errors as HTML pages | Problem details (RFC 9457) under `/api` | A client reads an error as it reads a resource. |
| The form, Stimulus and Composer examples | The `Book` resource | The example shows what an API needs. |
| `symfony/form`, `symfony/security-csrf` | None | A stateless API has no form and no session to protect. |
| No API contract | The OpenAPI document checked by `castor lint:openapi` and a test | The API stays as its document describes it. |

The rest stays: Symfony 8.2 from its release candidate, PHP 8.5, Castor only, the gates of `phil/dev-tools`, the five
test suites, the code coverage at 100 %, FrankenPHP in worker mode, Sentry in production, English and French pages, the
PHIL identity and the credit to Strangebuzz.

## Notes 📒

### Adding a resource

Create an entity with `bin/console make:entity --api-resource`, or a class of `src/ApiResource/` with a state provider
and processor for what Doctrine does not keep. Give each operation that writes a `security` expression on a scope,
declare that scope in `config/packages/phil_api_keys.yaml`, write the migration through the schema API, as
[the first one](migrations/Version20261125120000.php) does, and add its tests under `tests/Api/`.

### Accounts

The owners of the tokens are the clients of a user provider in memory. An API with accounts replaces it with its own
provider, an entity provider for instance, and the members create their tokens on the page of
`phil/api-keys-bundle`; the bundle also declares other owner types, such as organisations or applications.

### Idempotency and deprecations

`phil/api-contract-bundle` keeps the idempotency keys in the table `phil_idempotency`: a controller marked
`#[Idempotent]` runs once per `Idempotency-Key` and replays its response. A controller marked `#[DeprecatedEndpoint]`
announces its `Deprecation` and `Sunset` headers.

### YAML configuration files

The configuration of Symfony, of API Platform and of the bundles is in YAML, under `config/`:
`config/packages/*.yaml`, with `when@dev`, `when@test` and `when@prod` blocks for what changes by environment,
`config/routes.yaml`, `config/routes/*.yaml` and `config/services.yaml`. A Flex recipe adds its files in the same
format. Only `config/bundles.php` and `config/preload.php` stay PHP. `castor lint:yaml` checks every file of
`config/`, and `castor lint:container` checks the container they build.

```yaml
# config/packages/phil_api_keys.yaml
phil_api_keys:
    scopes:
        'books:write':
            description: 'Create, change and delete the books'
```

### Languages

The home page is in English, the default locale, and in French, chosen from the browser's `Accept-Language` header on
the same address. The messages are in [translations/](translations).

### Sentry

In the production environment, the errors go to [Sentry](https://sentry.io) once `SENTRY_DSN` holds the DSN of your
project, set as a real environment variable or a secret, never in a committed file. While it is empty, nothing is sent.

## What it doesn't ship? ❌

* GraphQL: the API is REST only
* Accounts, an administration and pages beyond the home page: start from
  [WebSymfony](https://github.com/parler-haut-interagir-librement/WebSymfony) (`phil/websymfony`) or
  [WebSkeleton](https://github.com/parler-haut-interagir-librement/WebSkeleton) (`phil/webskeleton`)
* A deployment: each API brings its own

## PHIL bundles 🧩

The bundles of [PHIL](https://ph-il.ca) add what an API needs, one at a time, with `composer require`. A few that suit
an API:

* [`phil/rate-limit-bundle`](https://packagist.org/packages/phil/rate-limit-bundle) and
  [`phil/usage-meter-bundle`](https://packagist.org/packages/phil/usage-meter-bundle): rate limits and quotas by token,
  with the `RateLimit` headers
* [`phil/ip-anonymizer`](https://packagist.org/packages/phil/ip-anonymizer): the address a token was last used from,
  anonymized
* [`phil/browser-security-bundle`](https://packagist.org/packages/phil/browser-security-bundle): the security headers
  of the responses
* [`phil/well-known-bundle`](https://packagist.org/packages/phil/well-known-bundle): `security.txt` and the other
  files of `/.well-known/`

The [PHIL organization on GitHub](https://github.com/parler-haut-interagir-librement) lists every package.

## References 📚

* [API Platform: the documentation](https://api-platform.com/docs/) (api-platform.com)
* [Testing the API](https://api-platform.com/docs/core/testing/) (api-platform.com)
* [RFC 9457: Problem Details for HTTP APIs](https://www.rfc-editor.org/rfc/rfc9457) (rfc-editor.org)
* [RFC 6750: Bearer Token Usage](https://www.rfc-editor.org/rfc/rfc6750) (rfc-editor.org)
* [Introducing the MicroSymfony application template](https://www.strangebuzz.com/en/blog/introducing-the-microsymfony-application-template) (strangebuzz.com)
* [Organizing your Symfony project tests](https://www.strangebuzz.com/en/blog/organizing-your-symfony-project-tests) (strangebuzz.com)
* [Castor, a journey across the sea of task runners](https://jolicode.com/blog/castor-a-journey-across-the-sea-of-task-runners) (jolicode.com)
* [The Symfony Framework Best Practices](https://symfony.com/doc/current/best_practices.html) (symfony.com)

## Others “Proclaim Honestly, Interact Liberally” Skeletons 🩻

* [MicroSymfony](https://github.com/parler-haut-interagir-librement/MicroSymfony) (`phil/microsymfony`): the micro
  template ApiSymfony derives from, with no database, Pico CSS, Stimulus and Turbo, Castor, the tests and quality gates.
* [WebSymfony](https://github.com/parler-haut-interagir-librement/WebSymfony) (`phil/websymfony`): MicroSymfony with
  Doctrine, accounts, EasyAdmin, KnpMenu, NelmioSecurityBundle and NelmioCorsBundle, still with Pico CSS.
* [WebSkeleton](https://github.com/parler-haut-interagir-librement/WebSkeleton) (`phil/webskeleton`): WebSymfony and the
  PHIL tools, organised in DDD (Domain, Application, Infrastructure), in French and in English (`/fr`, `/en`).

## Contributing 🤝

See [CONTRIBUTING](CONTRIBUTING.md) and the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security 🧯

See [SECURITY](SECURITY.md).

## Credits 🙏

* [Philippe Gamache](https://ph-il.ca), maintainer, for [Parler Haut, Interagir Librement](https://ph-il.ca)
* [COil](https://github.com/COil), author of [strangebuzz/MicroSymfony](https://github.com/strangebuzz/MicroSymfony),
  and [its contributors](https://github.com/strangebuzz/MicroSymfony/graphs/contributors)
* [All the contributors of ApiSymfony](https://github.com/parler-haut-interagir-librement/ApiSymfony/graphs/contributors)

## License ⚖️

The MIT License, with the attribution to the original repository it asks for. See the [license file](LICENSE).

## Built with ApiSymfony 🛠️

Nothing listed yet: an API built with ApiSymfony is welcome here, by a pull request.
