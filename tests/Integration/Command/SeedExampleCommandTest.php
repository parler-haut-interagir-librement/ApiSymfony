<?php

declare(strict_types=1);

namespace App\Tests\Integration\Command;

use App\Command\SeedExampleCommand;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Tests\Factory\BookFactory;
use Phil\ApiKeysBundle\Application\ApiTokens;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * The example data: the books in an empty database only, a new token on every run, printed alone on the last line.
 */
#[CoversClass(SeedExampleCommand::class)]
final class SeedExampleCommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItAddsTheBooksAndCreatesATokenThatOpensTheWrites(): void
    {
        $tester = $this->tester();

        self::assertSame(Command::SUCCESS, $tester->execute([]));

        $lines = explode("\n", trim($tester->getDisplay()));
        self::assertCount(3, $lines);
        self::assertSame('5 example books added.', $lines[0]);
        self::assertStringStartsWith('Token ', $lines[1]);
        self::assertStringContainsString(' created for user example-client, expiring ', $lines[1]);
        self::assertStringStartsWith('apisf_', $lines[2]);

        $books = $this->books()->findBy([], ['title' => 'ASC']);
        self::assertSame(
            ['Anne of Green Gables', 'Frankenstein; or, The Modern Prometheus', 'Les Misérables', 'Moby-Dick; or, The Whale', 'Pride and Prejudice'],
            array_map(static fn (Book $book): string => $book->getTitle(), $books),
        );
        self::assertSame('Lucy Maud Montgomery', $books[0]->getAuthor());
        self::assertSame('1908-06-13', $books[0]->getPublishedOn()?->format('Y-m-d'));

        $token = $this->apiTokens()->verify($lines[2]);
        self::assertSame(SeedExampleCommand::OWNER, $token->owner());
        self::assertSame([SeedExampleCommand::SCOPE], $token->scopes());
        self::assertSame(SeedExampleCommand::TOKEN_NAME, $token->name());
        self::assertStringContainsString($token->id(), $lines[1]);
    }

    public function testItLeavesADatabaseThatHoldsBooksAsItIs(): void
    {
        BookFactory::createOne(['title' => 'Les Belles-sœurs', 'author' => 'Michel Tremblay']);
        $tester = $this->tester();

        self::assertSame(Command::SUCCESS, $tester->execute([]));

        self::assertStringStartsWith('The database already holds books: no example book added.', $tester->getDisplay());
        self::assertSame(1, $this->books()->count());
        self::assertCount(1, $this->apiTokens()->find(SeedExampleCommand::OWNER));
    }

    public function testTheTokenExpiresWhenTold(): void
    {
        $tester = $this->tester();

        self::assertSame(Command::SUCCESS, $tester->execute(['--expires' => '+365 days']));

        $tokens = $this->apiTokens()->find(SeedExampleCommand::OWNER);
        self::assertCount(1, $tokens);
        $lifetime = $tokens[0]->expiresAt()->getTimestamp() - $tokens[0]->createdAt()->getTimestamp();
        self::assertSame(365 * 86_400, $lifetime);
    }

    public function testAnExpiryThatIsNoDateIsRefused(): void
    {
        $tester = $this->tester();

        self::assertSame(Command::INVALID, $tester->execute(['--expires' => 'next blue moon']));

        self::assertSame('The expiry is a date, or a relative time such as "+30 days".', trim($tester->getDisplay()));
        self::assertSame(0, $this->books()->count());
        self::assertSame([], $this->apiTokens()->find(SeedExampleCommand::OWNER));
    }

    public function testAnExpiryBeyondTheLongestLifetimeIsRefused(): void
    {
        $tester = $this->tester();

        self::assertSame(Command::INVALID, $tester->execute(['--expires' => '+10 years']));

        self::assertStringStartsWith('A token lasts at most ', $tester->getDisplay());
        self::assertSame(0, $this->books()->count());
        self::assertSame([], $this->apiTokens()->find(SeedExampleCommand::OWNER));
    }

    public function testItFailsWithoutAnApplicationToCreateTheToken(): void
    {
        self::bootKernel();
        $command = self::getContainer()->get(SeedExampleCommand::class);
        self::assertInstanceOf(SeedExampleCommand::class, $command);
        $tester = new CommandTester($command);

        self::assertSame(Command::FAILURE, $tester->execute([]));
        self::assertSame(0, $this->books()->count());
    }

    private function tester(): CommandTester
    {
        $application = new Application(self::bootKernel());

        return new CommandTester($application->find('app:example:seed'));
    }

    private function books(): BookRepository
    {
        $books = self::getContainer()->get(BookRepository::class);
        self::assertInstanceOf(BookRepository::class, $books);

        return $books;
    }

    private function apiTokens(): ApiTokens
    {
        $tokens = self::getContainer()->get(ApiTokens::class);
        self::assertInstanceOf(ApiTokens::class, $tokens);

        return $tokens;
    }
}
