<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

/**
 * Cross-origin requests, allowed on the API only, from the origins of CORS_ALLOW_ORIGIN.
 *
 * @see https://symfony.com/bundles/NelmioCorsBundle/current/index.html
 */
return App::config([
    'nelmio_cors' => [
        'defaults' => [
            'origin_regex' => true,
            'allow_origin' => ['%env(CORS_ALLOW_ORIGIN)%'],
            'allow_methods' => ['GET', 'OPTIONS', 'POST', 'PATCH', 'DELETE'],
            'allow_headers' => ['Content-Type', 'Authorization'],
            'expose_headers' => ['Link'],
            'max_age' => 3600,
        ],
        'paths' => [
            '^/api(/|$)' => null,
        ],
    ],
]);
