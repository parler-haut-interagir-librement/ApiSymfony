# Changelog

All notable changes to this template are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). The template derives from
[MicroSymfony](https://github.com/parler-haut-interagir-librement/MicroSymfony), the PHIL version of
[strangebuzz/MicroSymfony](https://github.com/strangebuzz/MicroSymfony); its own changes are listed here.

## Unreleased

The next version is 8.2.0-RC2.

### Added

- The route smoke test of `phil/dev-tools` in the functional suite, `RouteSmokeTest`: every GET route that needs no
  parameter answers under 500, `/api/docs`, the entry point of the API and the GET collection of each resource
  included; only the error routes of API Platform, the profiler and the error preview are left out.

### Changed

- `phil/cors-bundle` ^0.1@alpha answers the cross-origin requests on `/api` instead of NelmioCorsBundle:
  `config/packages/phil_cors.yaml` keeps the origins of `CORS_ALLOW_ORIGIN`, the methods, the headers and the
  `max_age`, without credentials; preflights are answered before the firewall, every answer of `/api` varies on
  `Origin`, and `CorsTest` checks a preflight, a simple request and a refused origin. Until that version is published,
  a path repository resolves it to `../phil/packages/cors-bundle`, in a clone of the PHIL monorepo beside this one; the
  repository is removed at the release, which installs it from Packagist.
- The configuration back in YAML: `config/packages/*.yaml` with `when@dev`, `when@test` and `when@prod` blocks,
  `config/routes.yaml`, `config/routes/*.yaml` and `config/services.yaml`; only `config/bundles.php` and
  `config/preload.php` stay PHP. `castor lint:yaml` checks `config/` too.
- `phil/dev-tools` ^0.2.1@alpha. Until that version is published, a path repository resolves it to a `../dev-tools`
  clone beside this one; the repository is removed at the release, which installs it from Packagist.
- `castor.php` imports the common tasks of `phil/dev-tools` (`resources/castor/tasks.php`) and sets them through
  `PHIL_CASTOR`: it keeps only the tasks of this application (the servers, the database, the API token,
  `test:schema`, `lint:doctrine`, `lint:openapi`, `helpers:check-requirements`). The task names stay; `castor lint`
  runs `lint:doctrine` and `lint:openapi` after the common lints, `castor ci` runs `test:schema` after them, and
  `test:suite` is new. `test:coverage` reads the threshold itself: `bin/coverage-checker.php` is gone, and PHPUnit
  writes its coverage summary without colours.
- Every requirement of `composer.json` at its latest minor (`symfony/ux-turbo` ^3.8, `symfony/stimulus-bundle` ^3.8,
  `twig/twig` ^3.31, `symfony/flex` ^2.12, `phpunit/phpunit` ^13.4…), the Symfony components at `~8.2.0`, and the lock
  updated.

### Removed

- `nelmio/cors-bundle` and `config/packages/nelmio_cors.yaml`, replaced by `phil/cors-bundle`.
- `StaticRoutesSmokeTest`, replaced by `RouteSmokeTest`.

## [8.2.0-RC1] - 2026-11-25

### Added

- The application of MicroSymfony, without its form and Stimulus examples: the home page that shows the README in
  English or in French, the slug endpoint, the custom error template, the five test suites and the PHIL identity (the
  name, the logo, the copyright linked to ph-il.ca, the credit to Strangebuzz).
- Symfony 8.2 from its first release candidate, on PHP 8.5 or later, every other dependency at its latest version.
- `composer create-project phil/apisymfony`: the package is of type `project`.
- API Platform 5, REST only: JSON-LD and JSON for the resources, merge-patch for the changes, problem details
  (RFC 9457) for the errors, the OpenAPI document and its Swagger UI under `/api/docs`.
- An example resource, `Book`, on Doctrine ORM: read by everyone, created, changed and deleted with a token that holds
  the scope `books:write`.
- The API tokens of `phil/api-keys-bundle`: a stateless firewall on `/api` with the `access_token` authenticator of
  Symfony, the scopes as roles and attributes, refusals answered `401` and `403` as problem details, the owners kept
  by a user provider in memory, `castor token` to create one.
- `phil/api-contract-bundle`: problem details for the refusals of the firewall and for `ProblemException`,
  `#[Idempotent]` and `#[DeprecatedEndpoint]` ready for the controllers, and the OpenAPI document checked against the
  rules of an unversioned REST and JSON API, by `castor lint:openapi` and by a test.
- Doctrine ORM 3 with its migrations: SQLite by default for the Symfony binary, PostgreSQL in `compose.yaml`, MySQL
  documented; one migration, written through the schema API of Doctrine DBAL, runs on the three.
- NelmioCorsBundle: cross-origin requests on `/api`, from the origins of `CORS_ALLOW_ORIGIN`.
- Tasks run by Castor only: the servers, the migrations, an API token, tests by suite, coverage and its threshold,
  mutation testing, the taint analysis, every lint, every fixer, the local continuous integration and the git hooks.
- The quality gates of `phil/dev-tools`, each tool in its own `vendor-bin/` directory, the Doctrine mapping and the
  OpenAPI contract besides; tests that name what they cover, PHPUnit failing on a deprecation, a notice or a warning;
  the code coverage at 100 %.
- `compose.yaml`: FrankenPHP in worker mode, with its workers restarted when a file changes, and PostgreSQL.
- Sentry in the production environment, off while `SENTRY_DSN` is empty.
- A GitHub Actions workflow: security, tests on PHP 8.5 and 8.6 with the coverage threshold and the migrations against
  the Doctrine mapping (`castor test:schema`, also in `castor ci`), lints; the security files of `phil/dev-tools` in
  `.github/`.
- The README and its French mirror: the quick start, the API and its tokens, the tasks, the quality gates, the
  differences from MicroSymfony, what the template does not ship, the other PHIL templates and what is built with it.
