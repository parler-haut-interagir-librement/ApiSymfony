<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

/**
 * API Platform, REST only: JSON-LD and plain JSON for the resources, problem details (RFC 9457) for the errors, the
 * OpenAPI document and its Swagger UI under /api/docs.
 *
 * @see https://api-platform.com/docs/core/configuration/
 */
return App::config([
    'api_platform' => [
        'title' => 'ApiSymfony',
        'description' => 'The example API of the ApiSymfony template.',
        'version' => '1.0.0',
        'formats' => [
            'jsonld' => ['application/ld+json'],
            'json' => ['application/json'],
        ],
        'docs_formats' => [
            'jsonopenapi' => ['application/vnd.openapi+json'],
            'html' => ['text/html'],
        ],
        'error_formats' => [
            'jsonproblem' => ['application/problem+json'],
        ],
        'patch_formats' => [
            'json' => ['application/merge-patch+json'],
        ],
        'defaults' => [
            'stateless' => true,
            'cache_headers' => [
                'vary' => ['Content-Type', 'Authorization', 'Origin'],
            ],
        ],
        // The bearer tokens of phil/api-keys-bundle, offered by the "Authorize" button of the Swagger UI.
        'swagger' => [
            'api_keys' => [
                'bearer' => [
                    'name' => 'Authorization',
                    'type' => 'header',
                ],
            ],
        ],
    ],
]);
