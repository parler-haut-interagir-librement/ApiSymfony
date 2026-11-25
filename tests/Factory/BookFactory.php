<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Book;
use DateTimeImmutable;
use Override;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * Books for the tests.
 *
 * @extends PersistentObjectFactory<Book>
 *
 * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories
 */
final class BookFactory extends PersistentObjectFactory
{
    #[Override]
    public static function class(): string
    {
        return Book::class;
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    protected function defaults(): array
    {
        return [
            'title' => self::faker()->sentence(4),
            'author' => self::faker()->name(),
            'publishedOn' => DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }
}
