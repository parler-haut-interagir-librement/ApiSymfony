<?php

declare(strict_types=1);

namespace App\Tests\Api;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * The cross-origin rules of config/packages/phil_cors.yaml: the API answers the origins that CORS_ALLOW_ORIGIN matches
 * (localhost and 127.0.0.1 on any port), refuses the others, and the pages outside /api send no CORS header.
 */
#[CoversNothing]
final class CorsTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private const string ALLOWED_ORIGIN = 'http://localhost:5173';

    public function testAPreflightFromAnAllowedOriginIsAnsweredBeforeTheFirewall(): void
    {
        $client = self::createClient();
        $this->preflight($client, '/api/books', self::ALLOWED_ORIGIN, 'POST', 'Content-Type, Authorization');

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertResponseHeaderSame('Access-Control-Allow-Origin', self::ALLOWED_ORIGIN);
        self::assertResponseHeaderSame('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE');
        self::assertResponseHeaderSame('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        self::assertResponseHeaderSame('Access-Control-Max-Age', '3600');
        self::assertResponseNotHasHeader('Access-Control-Allow-Credentials');
        self::assertContains('Origin', $this->vary($client));
    }

    public function testAPreflightAsksForAMethodTheApiDoesNotAllow(): void
    {
        $client = self::createClient();
        $this->preflight($client, '/api/books', self::ALLOWED_ORIGIN, 'PUT');

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertResponseNotHasHeader('Access-Control-Allow-Origin');
        self::assertResponseNotHasHeader('Access-Control-Allow-Methods');
    }

    public function testAPreflightAsksForAHeaderTheApiDoesNotAllow(): void
    {
        $client = self::createClient();
        $this->preflight($client, '/api/books', self::ALLOWED_ORIGIN, 'POST', 'X-Custom');

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertResponseNotHasHeader('Access-Control-Allow-Origin');
    }

    public function testASimpleRequestFromAnAllowedOriginReadsTheApi(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/books', server: ['HTTP_ORIGIN' => 'http://127.0.0.1:8000', 'HTTP_ACCEPT' => 'application/ld+json']);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Access-Control-Allow-Origin', 'http://127.0.0.1:8000');
        self::assertResponseHeaderSame('Access-Control-Expose-Headers', 'Link');
        self::assertResponseNotHasHeader('Access-Control-Allow-Credentials');
        self::assertContains('Origin', $this->vary($client));
    }

    public function testTheEntryPointOfTheApiAnswersAnAllowedOrigin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api', server: ['HTTP_ORIGIN' => 'http://localhost', 'HTTP_ACCEPT' => 'text/html']);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Access-Control-Allow-Origin', 'http://localhost');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideForeignOrigins(): iterable
    {
        yield 'another host' => ['https://evil.example'];
        yield 'an allowed host followed by another domain' => ['http://localhost.evil.example'];
        yield 'an allowed host as a subdomain' => ['http://evil.localhost'];
        yield 'another scheme' => ['ftp://localhost'];
        yield 'the null origin' => ['null'];
    }

    #[DataProvider('provideForeignOrigins')]
    public function testAPreflightFromAForeignOriginIsRefused(string $origin): void
    {
        $client = self::createClient();
        $this->preflight($client, '/api/books', $origin, 'POST', 'Content-Type');

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertResponseNotHasHeader('Access-Control-Allow-Origin');
        self::assertResponseNotHasHeader('Access-Control-Allow-Methods');
        self::assertResponseNotHasHeader('Access-Control-Allow-Headers');
        self::assertResponseNotHasHeader('Access-Control-Max-Age');
    }

    #[DataProvider('provideForeignOrigins')]
    public function testASimpleRequestFromAForeignOriginGetsNoCorsHeader(string $origin): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/books', server: ['HTTP_ORIGIN' => $origin, 'HTTP_ACCEPT' => 'application/ld+json']);

        self::assertResponseIsSuccessful();
        self::assertResponseNotHasHeader('Access-Control-Allow-Origin');
        self::assertResponseNotHasHeader('Access-Control-Expose-Headers');
        self::assertContains('Origin', $this->vary($client));
    }

    public function testThePagesOutsideTheApiSendNoCorsHeader(): void
    {
        $client = self::createClient();
        $client->request('GET', '/', server: ['HTTP_ORIGIN' => self::ALLOWED_ORIGIN]);

        self::assertResponseIsSuccessful();
        self::assertResponseNotHasHeader('Access-Control-Allow-Origin');
    }

    public function testAPathThatOnlyStartsLikeTheApiIsLeftAlone(): void
    {
        $client = self::createClient();
        $this->preflight($client, '/apis', self::ALLOWED_ORIGIN, 'GET');

        self::assertResponseNotHasHeader('Access-Control-Allow-Origin');
        self::assertResponseNotHasHeader('Access-Control-Allow-Methods');
    }

    /**
     * @return list<string>
     */
    private function vary(KernelBrowser $client): array
    {
        $values = [];
        foreach ($client->getResponse()->headers->all('Vary') as $header) {
            foreach (explode(',', (string) $header) as $value) {
                $values[] = trim($value);
            }
        }

        return $values;
    }

    private function preflight(KernelBrowser $client, string $path, string $origin, string $method, ?string $headers = null): void
    {
        $server = ['HTTP_ORIGIN' => $origin, 'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => $method];
        if (null !== $headers) {
            $server['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] = $headers;
        }

        $client->request('OPTIONS', $path, server: $server);
    }
}
