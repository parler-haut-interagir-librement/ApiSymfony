<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

/**
 * Every error under /api answered as problem details (RFC 9457), and the OpenAPI document checked against the rules
 * of an unversioned REST and JSON API by `bin/console phil:api-contract:verify` and by OpenApiContractTest.
 *
 * @see https://github.com/parler-haut-interagir-librement/phil/tree/main/packages/api-contract-bundle/doc
 */
return App::config([
    'phil_api_contract' => [
        'verification' => [
            'document' => '%kernel.project_dir%/var/openapi.json',
            'level' => 'recommended',
        ],
    ],
]);
