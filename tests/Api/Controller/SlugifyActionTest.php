<?php

declare(strict_types=1);

namespace App\Tests\Api\Controller;

use App\Controller\SlugifyAction;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * An endpoint of the application beside the resources of API Platform: a plain controller that answers JSON.
 */
#[CoversClass(SlugifyAction::class)]
final class SlugifyActionTest extends WebTestCase
{
    public function testSlugifyAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/slugify?title=This IS the _-! Api SYMFONY  project', server: ['HTTP_ACCEPT_LANGUAGE' => 'en']);

        self::assertResponseIsSuccessful();
        self::assertJsonStringEqualsJsonString('{"slug":"this-is-the-api-symfony-project","locale":"en"}', (string) $client->getResponse()->getContent());
    }

    public function testAMissingTitleIsAProblem(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/slugify', server: ['HTTP_ACCEPT' => 'application/json']);

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
        $problem = json_decode((string) $client->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertIsArray($problem);
        self::assertSame(404, $problem['status'] ?? null);
        self::assertArrayHasKey('type', $problem);
    }
}
