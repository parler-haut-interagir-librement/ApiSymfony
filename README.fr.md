<!-- phil-dev source-sha256=433eebebabd91b43691a5d07b89996b7015630692d676a0647447662d8261d4b -->
# ApiSymfony 🕸

[![Latest Version](https://img.shields.io/packagist/v/phil/apisymfony.svg?style=flat-square)](https://packagist.org/packages/phil/apisymfony)
[![PHP Version Require](https://poser.pugx.org/phil/apisymfony/require/php?style=flat-square)](https://packagist.org/packages/phil/apisymfony)
[![Symfony Version](https://img.shields.io/badge/Symfony-8.2-black?logo=symfony)](https://symfony.com/8)
[![API Platform](https://img.shields.io/badge/API%20Platform-5-38a9b4)](https://api-platform.com)
[![PHPStan level](https://img.shields.io/badge/PHPStan-level%20max-success?style=flat-square)](phpstan.neon)
[![Software License](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status (GitHub)](https://img.shields.io/github/actions/workflow/status/parler-haut-interagir-librement/ApiSymfony/symfony.yml?branch=main&style=flat-square)](https://github.com/parler-haut-interagir-librement/ApiSymfony/actions?query=workflow%3ASymfony+branch%3Amain)

## À propos 🖋

ApiSymfony est un gabarit d'application [Symfony 8.2](https://symfony.com/8) pour une API REST, prêt à l'emploi : une
application [API Platform](https://api-platform.com) avec une ressource d'exemple, des jetons d'API à portées, les
erreurs en détails de problème, un document OpenAPI vérifié selon le contrat d'une API sans numéro de version, des tests
de tous les types et des portes de qualité strictes. Partez-en pour une API, supprimez l'exemple, gardez le reste.

Il dérive de [MicroSymfony](https://github.com/parler-haut-interagir-librement/MicroSymfony), la version PHIL de
[strangebuzz/MicroSymfony](https://github.com/strangebuzz/MicroSymfony), créé et maintenu par
[COil](https://github.com/COil) de [Strangebuzz](https://www.strangebuzz.com) : les mêmes tâches, les mêmes tests, les
mêmes portes et la même identité, avec API Platform et une base de données au lieu des exemples de pages. Ce qui
diffère, et pourquoi, est listé dans [Différences avec MicroSymfony](#différences-avec-microsymfony-).

## Table des matières 📖

* [Démo](#démo-)
* [Démarrage rapide](#démarrage-rapide-)
  * [Avec le binaire Symfony](#avec-le-binaire-symfony-)
  * [Avec FrankenPHP et PostgreSQL](#avec-frankenphp-et-postgresql-)
  * [Avec MySQL](#avec-mysql-)
* [Prérequis](#prérequis-)
* [Pile technologique](#pile-technologique-)
* [L'API](#lapi-)
* [Tâches](#tâches-)
* [Portes de qualité](#portes-de-qualité-)
* [Différences avec MicroSymfony](#différences-avec-microsymfony-)
* [Notes](#notes-)
* [Ce qu'il ne contient pas](#ce-quil-ne-contient-pas-)
* [Bundles PHIL](#bundles-phil-)
* [Références](#références-)
* [Autres gabarits « Parler Haut, Interagir Librement »](#autres-gabarits--parler-haut-interagir-librement--)
* [Contribuer](#contribuer-)
* [Sécurité](#sécurité-)
* [Crédits](#crédits-)
* [Licence](#licence-)
* [Construit avec ApiSymfony](#construit-avec-apisymfony-)

## Démo 🌈

Une démo d'ApiSymfony tournera dans le navigateur sur [ph-il.ca](https://ph-il.ca), avec les autres gabarits de PHIL.

## Démarrage rapide ⚡

Créez un projet à partir de la dernière version :

    composer create-project phil/apisymfony mon-api && cd mon-api

Le dépôt est aussi un dépôt modèle de GitHub : le bouton « Use this template » crée un dépôt à partir de la branche
main, dont chaque commit passe l'intégration continue.

### Avec le binaire Symfony 🎶

Il vous faut le [binaire Symfony](https://symfony.com/download#step-1-install-symfony-cli) et
[Castor](https://castor.jolicode.com/getting-started/installation/). La base de données est un fichier SQLite de
`var/`, migré avant le démarrage du serveur :

    castor start

Ouvrez [https://127.0.0.1:8000/api/docs](https://127.0.0.1:8000/api/docs) (si le port 8000 est libre) pour la Swagger
UI de l'API, ou [https://127.0.0.1:8000](https://127.0.0.1:8000) pour ce LISEZMOI. `castor stop` arrête le serveur.

### Avec FrankenPHP et PostgreSQL 🧟

Il vous faut [Docker](https://www.docker.com/). `compose.yaml` sert l'application avec
[FrankenPHP](https://frankenphp.dev/) en mode *worker*, qui démarre le noyau une fois et sert requête après requête,
et redémarre ses *workers* quand un fichier change ; la base de données est PostgreSQL 17, migrée une fois les services
démarrés :

    castor up

Ouvrez [https://localhost/api/docs](https://localhost/api/docs). `castor down` arrête les services. L'image de
`docker/Dockerfile` sert au développement : une API déployée à partir de ce gabarit apporte son propre déploiement.

### Avec MySQL 🐬

MySQL est l'autre base de données que le gabarit prend en charge. Dans `compose.yaml`, remplacez le service `database`
par une image `mysql:8.4` et donnez à `DATABASE_URL` la valeur :

    mysql://app:app@database:3306/app?serverVersion=8.4&charset=utf8mb4

Avec le binaire Symfony, donnez la même variable dans `.env.local`, avec l'hôte de votre serveur. La migration décrit
ses tables par l'API de schéma de Doctrine DBAL et s'écrit dans le SQL de la base où elle tourne : elle tourne sur
SQLite, PostgreSQL et MySQL sans changement.

## Prérequis ⚙

* [PHP 8.5](https://www.php.net/releases/8.5/fr.php) ou plus récent, avec les extensions de `composer.json`, et
  [Castor](https://castor.jolicode.com)
* Le [binaire Symfony](https://symfony.com/download), ou [Docker](https://www.docker.com)
* L'extension [Xdebug](https://xdebug.org/) ou [PCOV](https://github.com/krakjoe/pcov) pour le rapport de couverture
  du code

## Pile technologique 🔗

* [PHP 8.5](https://www.php.net/releases/8.5/fr.php)
* [Symfony 8.2](https://symfony.com/8), dès sa version candidate, avec [Symfony Security](https://symfony.com/doc/current/security.html)
* [API Platform 5](https://api-platform.com), REST seulement
* [Doctrine ORM 3 (DBAL 4)](https://www.doctrine-project.org) et ses migrations, sur SQLite, PostgreSQL ou MySQL
* [`phil/api-keys-bundle`](https://packagist.org/packages/phil/api-keys-bundle) et
  [`phil/api-contract-bundle`](https://packagist.org/packages/phil/api-contract-bundle)
* [`phil/cors-bundle`](https://packagist.org/packages/phil/cors-bundle) pour les requêtes d'autres origines
* [Twig 3](https://twig.symfony.com) et [Pico CSS 2](https://picocss.com) pour la page d'accueil, en anglais et en
  français
* [Sentry](https://docs.sentry.io/platforms/php/guides/symfony/) pour les erreurs de l'environnement de production
* [PHPUnit 13](https://phpunit.de) et [Foundry](https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html)
* [FrankenPHP](https://frankenphp.dev/) en mode *worker*, par Docker Compose

## L'API 🔌

### Les ressources

API Platform fait de chaque classe marquée `#[ApiResource]` des opérations REST sous `/api`. L'exemple est
[Book](src/Entity/Book.php), une entité Doctrine :

| Opération | Qui |
|---|---|
| `GET /api/books`, `GET /api/books/{id}` | Tout le monde |
| `POST /api/books`, `PATCH /api/books/{id}`, `DELETE /api/books/{id}` | Un jeton qui porte la portée `books:write` |

Les ressources s'écrivent en JSON-LD (`application/ld+json`) ou en JSON (`application/json`), selon l'en-tête
`Accept` ; une modification s'envoie en `application/merge-patch+json`. Les collections sont paginées, 30 éléments par
page. API Platform est configuré pour REST seulement : pas de GraphQL. La configuration est dans
[config/packages/api_platform.yaml](config/packages/api_platform.yaml).

### Les jetons

L'API est sans état : une requête porte un jeton de [`phil/api-keys-bundle`](https://packagist.org/packages/phil/api-keys-bundle)
dans son en-tête `Authorization`, vérifié par l'authentificateur `access_token` de Symfony. Un jeton s'affiche une
fois, à sa création ; la base ne garde que son empreinte à clé. Il porte des portées, expire, et peut être révoqué ou
renouvelé :

    castor token
    curl -X POST https://127.0.0.1:8000/api/books \
         -H "Authorization: Bearer apisf_…" -H "Content-Type: application/ld+json" \
         -d '{"title": "Kamouraska", "author": "Anne Hébert"}'

`castor token` crée un jeton pour `example-client`, le client que [config/packages/security.yaml](config/packages/security.yaml)
garde en mémoire, avec la portée `books:write`. Les portées sont déclarées dans
[config/packages/phil_api_keys.yaml](config/packages/phil_api_keys.yaml), et chaque opération qui écrit nomme celle
qu'elle exige dans son expression `security` : `is_granted('API_SCOPE', 'books:write')`.
`bin/console phil:api-keys:list`, `phil:api-keys:revoke` et `phil:api-keys:purge-expired` gèrent les jetons.

Les jetons sont hachés avec `API_KEYS_HASH_KEY`, 32 octets aléatoires ou plus. `.env.dev` et `.env.test` portent des
clés connues de tous, pour le poste de travail et les tests ; `.env` la laisse vide, pour qu'aucun autre environnement
ne démarre avec une clé connue. Donnez-la comme secret :

    php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;' | bin/console secrets:set API_KEYS_HASH_KEY -

### Les erreurs

Chaque erreur de l'API est un problème (RFC 9457), `application/problem+json` : API Platform écrit celles de ses
opérations, avec les violations des contraintes pour un `422` ; `phil/api-contract-bundle` écrit le `401` d'un jeton
absent ou refusé, avec son défi `WWW-Authenticate`, et chaque `ProblemException` qu'un contrôleur lance ; un `403` ne
donne aucune raison. Symfony écrit les autres en problèmes aussi pour un client qui accepte JSON, comme le `404` de
[SlugifyAction](src/Controller/SlugifyAction.php), un point d'accès à côté des ressources.

### Le document OpenAPI

API Platform publie le document OpenAPI de l'API à `/api/docs.jsonopenapi`, et sa Swagger UI à `/api/docs`, avec un
bouton « Authorize » pour le jeton. `castor lint:openapi` l'exporte dans `var/openapi.json` et le vérifie selon les
règles de [`phil/api-contract`](https://packagist.org/packages/phil/api-contract) pour une API REST et JSON sans numéro
de version : aucune version dans un chemin, des types de média JSON, des détails de problème pour les erreurs, et plus.
Il échoue sur un avertissement ; [OpenApiContractTest](tests/Integration/Api/OpenApiContractTest.php) fait la même
vérification avec les tests.

### Les requêtes d'autres origines

[`phil/cors-bundle`](https://packagist.org/packages/phil/cors-bundle) répond aux requêtes d'autres origines sur `/api`,
comme [config/packages/phil_cors.yaml](config/packages/phil_cors.yaml) les règle :

* les origines que `CORS_ALLOW_ORIGIN` reconnaît, une expression régulière ancrée par `^` et `$` : `localhost` et
  `127.0.0.1`, sur n'importe quel port, dans `.env` ; réglez les origines de vos interfaces dans `.env.local` ou dans
  l'environnement du serveur ;
* les méthodes `GET`, `POST`, `PATCH` et `DELETE`, les en-têtes de requête `Content-Type` et `Authorization`, l'en-tête
  de réponse `Link` lisible par la page, les requêtes de pré-vérification gardées une heure par le navigateur ;
* aucun justificatif d'identité : l'API lit un jeton porteur, jamais un témoin, donc aucune origine ne peut en envoyer
  un.

Le bundle répond aux requêtes de pré-vérification avant le routeur et le pare-feu, donc aucune route `OPTIONS` n'est
nécessaire, et ajoute `Vary: Origin` à chaque réponse de `/api`. Une autre origine ne reçoit aucun
`Access-Control-Allow-Origin`, ce que le navigateur lit comme un refus ; les pages hors de `/api` n'envoient aucun
en-tête CORS. Une origine générique avec justificatifs, un motif non ancré ou l'origine `null` arrête l'application à
sa première requête plutôt que d'ouvrir l'API. [CorsTest](tests/Api/CorsTest.php) vérifie ces règles.

Tant que `phil/cors-bundle` 0.1.0-alpha n'est pas publié, `composer.json` le résout par un dépôt de type chemin vers
`../phil/packages/cors-bundle`, dans un clone du monodépôt PHIL à côté de celui-ci ; ce dépôt est retiré à la sortie de
ce gabarit, qui installe alors `phil/cors-bundle` depuis Packagist.

## Tâches 🧰

`castor` liste toutes les tâches. Les tests, les vérifications, les correcteurs, `ci` et `hooks` sont les tâches
communes de [`phil/dev-tools`](https://packagist.org/packages/phil/dev-tools), que `castor.php` importe depuis
`vendor/phil/dev-tools/resources/castor/tasks.php` et règle par sa constante `PHIL_CASTOR` : les suites, le seuil de
couverture, et les contrôles de cette application que `lint` et `ci` appellent après les leurs (`lint:doctrine`,
`lint:openapi`, `test:schema`). `castor.php` ne garde que les tâches de cette application. Les principales :

| Tâche | Ce qu'elle fait |
|---|---|
| `castor start`, `castor stop` | Le serveur web du binaire Symfony, sur SQLite. |
| `castor up`, `castor down` | FrankenPHP en mode *worker* et PostgreSQL, avec Docker Compose. |
| `castor migrate` | Les migrations, sur la base de `DATABASE_URL`. |
| `castor token` | Un jeton d'API pour le client d'exemple, avec la portée `books:write`. |
| `castor test` | Toutes les suites PHPUnit ; `--filter` et `--options` passent à PHPUnit. |
| `castor test:unit`, `test:integration`, `test:api`, `test:functional`, `test:e2e` | Une suite ; `castor test:suite <nom>` lance n'importe quelle suite de `phpunit.dist.xml`. |
| `castor test:next` | Les tests sur la prochaine version de PHP, dans son image Docker. |
| `castor test:schema` | Les migrations sur une base SQLite neuve, puis son schéma comparé au *mapping* Doctrine. |
| `castor coverage` | Le rapport de couverture HTML dans `var/coverage/`, et son seuil. |
| `castor infection` | Les tests de mutation. |
| `castor taint` | L'analyse de souillure de Psalm. |
| `castor lint` | Toutes les vérifications, le *mapping* Doctrine, le contrat OpenAPI et l'analyse statique. |
| `castor fix` | Rector, PHP-CS-Fixer, Twig-CS-Fixer, Biome et composer-normalize. |
| `castor ci` | L'intégration continue, en local : la couverture, toutes les vérifications, puis `test:schema`. |
| `castor hooks` | Les crochets git : les vérifications rapides des fichiers indexés avant chaque commit. |

## Portes de qualité 🚦

Les vérifications viennent de [`phil/dev-tools`](https://packagist.org/packages/phil/dev-tools), l'outillage commun de
PHIL, adapté à une application. Chaque outil s'installe dans son propre répertoire `vendor-bin/<outil>/` par le
[plugiciel bin de Composer](https://github.com/bamarni/composer-bin-plugin), pour que ses dépendances ne croisent
jamais celles de l'application. Les fichiers de configuration minces de la racine (`phpstan.neon`, `rector.php`,
`.php-cs-fixer.dist.php`, `arkitect.php`) appellent les fichiers communs ; `phil-dev.php` porte ce qui est propre à
l'application, chaque exception avec sa raison.

| Tâche | Vérifie |
|---|---|
| `castor lint:composer` | L'analyse des dépendances (une classe employée sans que son paquet soit requis), `composer audit`, composer-unused et composer-normalize. |
| `castor lint:php` | PHP-CS-Fixer : `@Symfony`, `@Symfony:risky`, types stricts et comparaisons strictes. |
| `castor lint:twig` | `lint:twig`, puis le style des gabarits. |
| `castor lint:js` | Biome sur les contrôleurs Stimulus. |
| `castor lint:yaml`, `lint:container` | Les fichiers YAML, `config/` et les messages compris, et le conteneur. |
| `castor lint:doctrine` | Le *mapping* Doctrine. |
| `castor lint:openapi` | Le document OpenAPI selon le contrat d'une API REST et JSON sans numéro de version. |
| `castor stan` (`lint:analyse`) | PHPStan au niveau maximal, avec les extensions Doctrine et Symfony, la couverture des types à 100 %, une limite de complexité cognitive et des appels interdits. |
| `castor lint:rector` | Rector n'a plus rien à changer. |
| `castor lint:architecture` | Arkitect sur la forme des classes ; Deptrac sur les librairies que `src/` peut employer, listées dans `phil-dev.php`. |

La suite fonctionnelle contient le test de fumée des routes de `phil/dev-tools` (`RouteSmokeTesting`) : chaque route GET
qui n'exige aucun paramètre répond sans erreur de serveur, la page d'accueil, `/api/slugify`, `/api/docs`, le point
d'entrée de l'API et la collection GET de chaque ressource compris.

Tant que `phil/dev-tools` 0.2.1-alpha n'est pas publié, `composer.json` le résout par un dépôt de type chemin vers un
clone `../dev-tools` voisin de celui-ci ; ce dépôt est retiré à la sortie de ce gabarit, qui installe alors
`phil/dev-tools` depuis Packagist.

L'analyse de souillure de Psalm, `castor taint`, tourne à part, avant une sortie et une fois par semaine. Le
répertoire `.github/` porte aussi les fichiers de sécurité de `phil/dev-tools` : Dependabot, avec un délai de sept
jours, l'OpenSSF Scorecard et zizmor, qui audite les *workflows*.

## Différences avec MicroSymfony 🔀

| MicroSymfony | Ce gabarit | Pourquoi |
|---|---|---|
| Aucune base de données | Doctrine ORM et ses migrations : SQLite pour le binaire Symfony, PostgreSQL dans `compose.yaml`, MySQL documenté | Une API garde ses ressources et ses jetons. |
| Un point d'accès JSON dans un contrôleur | API Platform 5, REST seulement, à côté d'un contrôleur simple | Les opérations, la pagination, la négociation de contenu, la validation et le document OpenAPI viennent de la ressource. |
| Aucune sécurité | Un pare-feu sans état sur `/api`, des jetons d'API à portées | Une API publique sait qui l'appelle et pour quoi. |
| Les erreurs en pages HTML | Des détails de problème (RFC 9457) sous `/api` | Un client lit une erreur comme il lit une ressource. |
| Les exemples de formulaire, de Stimulus et de Composer | La ressource `Book` | L'exemple montre ce dont une API a besoin. |
| `symfony/form`, `symfony/security-csrf` | Aucun | Une API sans état n'a ni formulaire ni session à protéger. |
| Aucun contrat d'API | Le document OpenAPI vérifié par `castor lint:openapi` et un test | L'API reste telle que son document la décrit. |

Le reste demeure : Symfony 8.2 dès sa version candidate, PHP 8.5, Castor seulement, les portes de `phil/dev-tools`, les
cinq suites de tests, la couverture du code à 100 %, FrankenPHP en mode *worker*, Sentry en production, les pages en
anglais et en français, l'identité de PHIL et le crédit à Strangebuzz.

## Notes 📒

### Ajouter une ressource

Créez une entité avec `bin/console make:entity --api-resource`, ou une classe de `src/ApiResource/` avec un
fournisseur et un processeur d'état pour ce que Doctrine ne garde pas. Donnez à chaque opération qui écrit une
expression `security` sur une portée, déclarez cette portée dans `config/packages/phil_api_keys.yaml`, écrivez la
migration par l'API de schéma, comme [la première](migrations/Version20261125120000.php), et ajoutez ses tests sous
`tests/Api/`.

### Les comptes

Les titulaires des jetons sont les clients d'un fournisseur d'utilisateurs en mémoire. Une API avec des comptes le
remplace par son propre fournisseur, un fournisseur d'entités par exemple, et les membres créent leurs jetons sur la
page de `phil/api-keys-bundle` ; le bundle déclare aussi d'autres types de titulaires, comme des organisations ou des
applications.

### Idempotence et dépréciations

`phil/api-contract-bundle` garde les clés d'idempotence dans la table `phil_idempotency` : un contrôleur marqué
`#[Idempotent]` s'exécute une fois par `Idempotency-Key` et rejoue sa réponse. Un contrôleur marqué
`#[DeprecatedEndpoint]` annonce ses en-têtes `Deprecation` et `Sunset`.

### Fichiers de configuration YAML

La configuration de Symfony, d'API Platform et des bundles est en YAML, sous `config/` :
`config/packages/*.yaml`, avec des blocs `when@dev`, `when@test` et `when@prod` pour ce qui change selon
l'environnement, `config/routes.yaml`, `config/routes/*.yaml` et `config/services.yaml`. Une recette Flex ajoute ses
fichiers dans le même format. Seuls `config/bundles.php` et `config/preload.php` restent en PHP. `castor lint:yaml`
vérifie chaque fichier de `config/`, et `castor lint:container` vérifie le conteneur qu'ils construisent.

```yaml
# config/packages/phil_api_keys.yaml
phil_api_keys:
    scopes:
        'books:write':
            description: 'Create, change and delete the books'
```

### Langues

La page d'accueil est en anglais, la langue par défaut, et en français, choisie selon l'en-tête `Accept-Language` du
navigateur, à la même adresse. Les messages sont dans [translations/](translations).

### Sentry

Dans l'environnement de production, les erreurs vont à [Sentry](https://sentry.io) dès que `SENTRY_DSN` porte le DSN de
votre projet, donné comme vraie variable d'environnement ou comme secret, jamais dans un fichier commité. Tant qu'elle
est vide, rien n'est envoyé.

## Ce qu'il ne contient pas ❌

* GraphQL : l'API est REST seulement
* Des comptes, une administration et des pages au-delà de la page d'accueil : partez de
  [WebSymfony](https://github.com/parler-haut-interagir-librement/WebSymfony) (`phil/websymfony`) ou de
  [WebSkeleton](https://github.com/parler-haut-interagir-librement/WebSkeleton) (`phil/webskeleton`)
* Un déploiement : chaque API apporte le sien

## Bundles PHIL 🧩

Les bundles de [PHIL](https://ph-il.ca) ajoutent ce dont une API a besoin, un à la fois, par `composer require`.
Quelques-uns qui conviennent à une API :

* [`phil/rate-limit-bundle`](https://packagist.org/packages/phil/rate-limit-bundle) et
  [`phil/usage-meter-bundle`](https://packagist.org/packages/phil/usage-meter-bundle) : des limites de débit et des
  quotas par jeton, avec les en-têtes `RateLimit`
* [`phil/ip-anonymizer`](https://packagist.org/packages/phil/ip-anonymizer) : l'adresse de la dernière utilisation d'un
  jeton, anonymisée
* [`phil/browser-security-bundle`](https://packagist.org/packages/phil/browser-security-bundle) : les en-têtes de
  sécurité des réponses
* [`phil/well-known-bundle`](https://packagist.org/packages/phil/well-known-bundle) : `security.txt` et les autres
  fichiers de `/.well-known/`

L'[organisation PHIL sur GitHub](https://github.com/parler-haut-interagir-librement) liste tous les paquets.

## Références 📚

* [API Platform : la documentation](https://api-platform.com/docs/) (api-platform.com)
* [Tester l'API](https://api-platform.com/docs/core/testing/) (api-platform.com)
* [RFC 9457 : Problem Details for HTTP APIs](https://www.rfc-editor.org/rfc/rfc9457) (rfc-editor.org)
* [RFC 6750 : Bearer Token Usage](https://www.rfc-editor.org/rfc/rfc6750) (rfc-editor.org)
* [Introducing the MicroSymfony application template](https://www.strangebuzz.com/en/blog/introducing-the-microsymfony-application-template) (strangebuzz.com)
* [Organizing your Symfony project tests](https://www.strangebuzz.com/en/blog/organizing-your-symfony-project-tests) (strangebuzz.com)
* [Castor, a journey across the sea of task runners](https://jolicode.com/blog/castor-a-journey-across-the-sea-of-task-runners) (jolicode.com)
* [The Symfony Framework Best Practices](https://symfony.com/doc/current/best_practices.html) (symfony.com)

## Autres gabarits « Parler Haut, Interagir Librement » 🩻

* [MicroSymfony](https://github.com/parler-haut-interagir-librement/MicroSymfony) (`phil/microsymfony`) : le gabarit
  micro dont ApiSymfony dérive, sans base de données, avec Pico CSS, Stimulus et Turbo, Castor, les tests et les portes
  de qualité.
* [WebSymfony](https://github.com/parler-haut-interagir-librement/WebSymfony) (`phil/websymfony`) : MicroSymfony avec
  Doctrine, des comptes, EasyAdmin, KnpMenu, NelmioSecurityBundle et NelmioCorsBundle, toujours avec Pico CSS.
* [WebSkeleton](https://github.com/parler-haut-interagir-librement/WebSkeleton) (`phil/webskeleton`) : WebSymfony et
  les outils de PHIL, organisé en DDD (Domain, Application, Infrastructure), en français et en anglais (`/fr`, `/en`).

## Contribuer 🤝

Voir [CONTRIBUTING](CONTRIBUTING.md) et le [Code de conduite](CODE_OF_CONDUCT.md).

## Sécurité 🧯

Voir [SECURITY](SECURITY.md).

## Crédits 🙏

* [Philippe Gamache](https://ph-il.ca), mainteneur, pour [Parler Haut, Interagir Librement](https://ph-il.ca)
* [COil](https://github.com/COil), auteur de [strangebuzz/MicroSymfony](https://github.com/strangebuzz/MicroSymfony),
  et [ses contributeurs](https://github.com/strangebuzz/MicroSymfony/graphs/contributors)
* [Tous les contributeurs d'ApiSymfony](https://github.com/parler-haut-interagir-librement/ApiSymfony/graphs/contributors)

## Licence ⚖️

The MIT License, avec l'attribution au dépôt d'origine qu'elle demande. Voir le [fichier de licence](LICENSE).

## Construit avec ApiSymfony 🛠️

Rien pour l'instant : une API construite avec ApiSymfony est la bienvenue ici, par une demande de tirage.
