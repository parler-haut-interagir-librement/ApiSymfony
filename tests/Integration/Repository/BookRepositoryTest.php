<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Tests\Factory\BookFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * The repository of the books, the one Doctrine gives for the entity.
 */
#[CoversClass(BookRepository::class)]
final class BookRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItFindsTheBooksDoctrineKeeps(): void
    {
        $book = BookFactory::createOne(['title' => 'Les Belles-sœurs', 'author' => 'Michel Tremblay']);

        $repository = self::getContainer()->get(BookRepository::class);
        self::assertInstanceOf(BookRepository::class, $repository);
        self::assertSame($repository, self::getContainer()->get('doctrine')->getRepository(Book::class));

        $found = $repository->findOneBy(['title' => 'Les Belles-sœurs']);
        self::assertInstanceOf(Book::class, $found);
        self::assertTrue($book->getId()->equals($found->getId()));
        self::assertSame('Michel Tremblay', $found->getAuthor());
    }
}
