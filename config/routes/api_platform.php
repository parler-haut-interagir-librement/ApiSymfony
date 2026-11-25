<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/*
 * The operations of the API resources, their documentation and their entry point, under /api.
 */
return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('.', 'api_platform')->prefix('/api');
};
