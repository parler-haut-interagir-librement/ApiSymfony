<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Controller\HomeAction;
use App\Controller\SlugifyAction;
use App\Entity\Book;
use Phil\DevTools\Testing\RouteSmokeTesting;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Every GET route that needs no parameter answers without a server error: the home page, the slug endpoint, the
 * OpenAPI document under /api/docs, the entry point of the API and the GET collection of each resource.
 *
 * The collections of API Platform are routes named "_api_…": only its error routes are left out, with those of the
 * profiler and the error preview.
 */
#[CoversClass(HomeAction::class)]
#[CoversClass(SlugifyAction::class)]
#[CoversClass(Book::class)]
final class RouteSmokeTest extends WebTestCase
{
    use ResetDatabase;
    use RouteSmokeTesting;

    /**
     * @return list<string>
     */
    protected static function smokeRouteExclusions(): array
    {
        return ['_api_errors', '_api_validation_errors', '_wdt', '_profiler', '_preview_error'];
    }
}
