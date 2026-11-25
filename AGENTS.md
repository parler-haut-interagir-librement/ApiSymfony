# AGENTS.md - ApiSymfony development guidelines

Guidelines for coding assistants working on this repository, the ApiSymfony application template of PHIL
(`phil/apisymfony`): a REST API on API Platform, derived from
[MicroSymfony](https://github.com/parler-haut-interagir-librement/MicroSymfony).

## Project overview

A Symfony 8.2 application template on PHP 8.5 or later, with API Platform 5 (REST only: JSON-LD and JSON, problem
details for the errors, the OpenAPI document and its Swagger UI), Doctrine ORM on SQLite, PostgreSQL or MySQL, the API
tokens of `phil/api-keys-bundle` and the contract checks of `phil/api-contract-bundle`. The home page, in English and
French, shows the README with Pico CSS. It keeps the usual layout of a Symfony application: no layers, no domain
directories.

## Tasks

Every task runs through [Castor](https://castor.jolicode.com) (`castor.php`); `composer.json` holds no task script.

```bash
castor start                     # the Symfony binary's web server, on SQLite
castor up                        # FrankenPHP in worker mode and PostgreSQL with Docker Compose
castor migrate                   # the migrations on the database of DATABASE_URL
castor token                     # an API token for the example client, with the scope books:write
castor test                      # every PHPUnit suite
castor test:api                  # one suite: unit, integration, api, functional, e2e
castor test --filter=testSlugify # one test
castor coverage                  # the coverage report and its threshold
castor lint                      # every lint, the OpenAPI contract and the static analysis
castor stan                      # PHPStan only
castor fix                       # every fixer
castor ci                        # coverage, then every lint
```

## Code style

1. `declare(strict_types=1);` in every PHP file.
2. Classes are `final` unless they are made to be extended; services take `readonly` promoted properties.
3. Every parameter, return and property is typed natively; a PHPDoc type only refines a native one.
4. An API resource is an entity or a class of `src/ApiResource/` with `#[ApiResource]`; a route that is no resource
   follows the action pattern: one `*Action` class per route, with `__invoke()`.
5. Every operation that writes declares a `security` expression on a scope of the token: `is_granted('API_SCOPE', …)`.
6. Dates are never compared with `==` nor `===`: compare their timestamps or use `<` and `>`.
7. Migrations describe their tables through the schema API of Doctrine DBAL, so that they run on every platform.
8. Configuration files are PHP, under `config/`.

## Tests

One directory per kind of test under `tests/`, mirroring `src/`: `Unit` (no kernel), `Integration` (the kernel),
`Api` (requests to the API, with the `ApiTestCase` of API Platform and the factories of Foundry), `Functional` (pages
through the kernel), `E2E` (a browser, once Panther is installed). Every test class names what it covers with
`#[CoversClass]` or `#[CoversNothing]`; PHPUnit fails on a deprecation, a notice or a warning.

## Quality gates

Before a change is complete, `castor ci` passes: the tests at the coverage threshold, PHP-CS-Fixer, Twig-CS-Fixer,
Biome, the Doctrine mapping, the OpenAPI document against the rules of `phil/api-contract`, PHPStan at the maximum
level with the rules of `phil/dev-tools`, Rector, Arkitect, Deptrac, the dependency analysis, `composer audit`,
composer-unused and composer-normalize. An exception to a gate goes in `phil-dev.php`, with its reason.

## Security

- Never commit a secret: `.env` holds defaults for development only, and `API_KEYS_HASH_KEY` is empty there.
- Validate every input with constraints on the resource.
- `castor lint:composer` runs `composer audit`; Composer refuses a version with a known advisory.
