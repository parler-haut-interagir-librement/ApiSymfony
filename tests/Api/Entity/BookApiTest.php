<?php

declare(strict_types=1);

namespace App\Tests\Api\Entity;

use ApiPlatform\Test\ApiTestCase;
use App\Entity\Book;
use App\Tests\Factory\BookFactory;
use DateTimeImmutable;
use Phil\ApiKeysBundle\Application\ApiTokens;
use PHPUnit\Framework\Attributes\CoversClass;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * The operations of the books: everyone reads, a token with the scope "books:write" writes, and every refusal is a
 * problem (RFC 9457).
 *
 * @see https://api-platform.com/docs/core/testing/
 */
#[CoversClass(Book::class)]
final class BookApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testEveryoneReadsTheBooks(): void
    {
        BookFactory::createOne(['title' => 'Le Petit Prince', 'author' => 'Antoine de Saint-Exupéry']);
        BookFactory::createOne(['title' => 'Kamouraska', 'author' => 'Anne Hébert']);

        self::createClient()->request('GET', '/api/books');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/ld+json');
        self::assertJsonContains([
            'totalItems' => 2,
            'member' => [['title' => 'Kamouraska'], ['title' => 'Le Petit Prince']],
        ]);
    }

    public function testEveryoneReadsABook(): void
    {
        $book = BookFactory::createOne(['title' => 'Bonheur d’occasion', 'author' => 'Gabrielle Roy', 'publishedOn' => new DateTimeImmutable('1945-06-01')]);

        self::createClient()->request('GET', '/api/books/'.$book->getId(), ['headers' => ['Accept' => 'application/json']]);

        self::assertResponseIsSuccessful();
        self::assertJsonEquals([
            'id' => (string) $book->getId(),
            'title' => 'Bonheur d’occasion',
            'author' => 'Gabrielle Roy',
            'publishedOn' => '1945-06-01T00:00:00+00:00',
        ]);
    }

    public function testAnUnknownBookIsAProblem(): void
    {
        self::createClient()->request('GET', '/api/books/0193a2c4-0000-7000-8000-000000000000');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
        self::assertJsonContains(['status' => 404]);
    }

    public function testWritingWithoutATokenIsRefused(): void
    {
        self::createClient()->request('POST', '/api/books', ['json' => ['title' => 'Maria Chapdelaine', 'author' => 'Louis Hémon']]);

        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('WWW-Authenticate', 'Bearer realm="api"');
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
        self::assertSame(0, BookFactory::count());
    }

    public function testAnUnknownTokenIsRefused(): void
    {
        self::createClient()->request('POST', '/api/books', [
            'auth_bearer' => 'apisf_000000000000_00000000000000000000000000000000000000',
            'json' => ['title' => 'Maria Chapdelaine', 'author' => 'Louis Hémon'],
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('WWW-Authenticate', 'Bearer realm="api",error="invalid_token"');
        self::assertSame(0, BookFactory::count());
    }

    public function testATokenWithTheScopeCreatesChangesAndDeletesABook(): void
    {
        $client = self::createClient();
        $token = $this->token(['books:write']);

        $created = $client->request('POST', '/api/books', [
            'auth_bearer' => $token,
            'json' => ['title' => 'Maria Chapdelaine', 'author' => 'Louis Hémon', 'publishedOn' => '1913-01-27'],
        ]);
        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains(['title' => 'Maria Chapdelaine', 'author' => 'Louis Hémon']);
        $location = $created->toArray()['@id'];
        self::assertIsString($location);

        $client->request('PATCH', $location, [
            'auth_bearer' => $token,
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['title' => 'Maria Chapdelaine, récit du Canada français'],
        ]);
        self::assertResponseIsSuccessful();
        self::assertJsonContains(['title' => 'Maria Chapdelaine, récit du Canada français', 'author' => 'Louis Hémon']);

        $client->request('DELETE', $location, ['auth_bearer' => $token]);
        self::assertResponseStatusCodeSame(204);
        self::assertSame(0, BookFactory::count());
    }

    public function testATokenWithoutTheScopeIsForbidden(): void
    {
        $book = BookFactory::createOne();

        self::createClient()->request('DELETE', '/api/books/'.$book->getId(), ['auth_bearer' => $this->token([])]);

        self::assertResponseStatusCodeSame(403);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
        self::assertSame(1, BookFactory::count());
    }

    public function testAnInvalidBookIsAProblemThatListsTheViolations(): void
    {
        self::createClient()->request('POST', '/api/books', [
            'auth_bearer' => $this->token(['books:write']),
            'json' => ['title' => '', 'author' => str_repeat('a', 256)],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
        self::assertJsonContains([
            'status' => 422,
            'violations' => [
                ['propertyPath' => 'title'],
                ['propertyPath' => 'author'],
            ],
        ]);
        self::assertSame(0, BookFactory::count());
    }

    /**
     * @param list<string> $scopes
     */
    private function token(array $scopes): string
    {
        $tokens = self::getContainer()->get(ApiTokens::class);
        self::assertInstanceOf(ApiTokens::class, $tokens);

        return $tokens->create('example-client', 'A test', $scopes)->plainText;
    }
}
