<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

/**
 * The tokens of the API: shown once, kept as a keyed hash, scoped and expiring. Create one with
 * `bin/console phil:api-keys:create example-client "My script" --scope=books:write`.
 *
 * API_KEYS_HASH_KEY holds 32 random bytes or more, from the secrets vault outside of development.
 *
 * @see https://github.com/parler-haut-interagir-librement/phil/tree/main/packages/api-keys-bundle/doc
 */
return App::config([
    'phil_api_keys' => [
        'prefix' => 'apisf',
        'hash_key' => '%env(API_KEYS_HASH_KEY)%',
        'realm' => 'api',
        'scopes' => [
            'books:write' => ['description' => 'Create, change and delete the books'],
        ],
    ],
]);
