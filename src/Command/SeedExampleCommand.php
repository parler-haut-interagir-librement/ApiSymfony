<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Book;
use App\Repository\BookRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *     bin/console app:example:seed
 *     bin/console app:example:seed --expires="+365 days".
 *
 * Fills an empty database with a few example books and creates an API token for the example client, with the scope
 * "books:write", so that the API can be tried at once: reading the books, then writing with the token. The books are
 * added only when the database holds none. The token comes from `phil:api-keys:create`: a new one on every run, its
 * text printed once, alone on the last line, for a script to read it. Nothing is added when the token is refused.
 *
 * @see \App\Tests\Integration\Command\SeedExampleCommandTest
 */
#[AsCommand(name: 'app:example:seed', description: 'Adds example books to an empty database and creates an API token for the example client')]
final class SeedExampleCommand extends Command
{
    /** The owner of the token: a client of the provider in memory of config/packages/security.yaml. */
    public const string OWNER = 'example-client';

    /** The scope of the token: the one the operations that write on the books require. */
    public const string SCOPE = 'books:write';

    /** The name the token is listed under. */
    public const string TOKEN_NAME = 'Example token';

    /**
     * The example books, in the public domain: title, author and date of first publication.
     *
     * @var list<array{string, string, string}>
     */
    public const array BOOKS = [
        ['Pride and Prejudice', 'Jane Austen', '1813-01-28'],
        ['Frankenstein; or, The Modern Prometheus', 'Mary Shelley', '1818-01-01'],
        ['Moby-Dick; or, The Whale', 'Herman Melville', '1851-10-18'],
        ['Les Misérables', 'Victor Hugo', '1862-04-03'],
        ['Anne of Green Gables', 'Lucy Maud Montgomery', '1908-06-13'],
    ];

    public function __construct(
        private readonly BookRepository $books,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    #[Override]
    protected function configure(): void
    {
        $this->addOption('expires', null, InputOption::VALUE_REQUIRED, 'When the token expires: a date, or a relative time such as "+30 days"; the default lifetime without it');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $arguments = ['owner' => self::OWNER, 'name' => self::TOKEN_NAME, '--scope' => [self::SCOPE]];
        $expires = $input->getOption('expires');
        if (\is_string($expires)) {
            $arguments['--expires'] = $expires;
        }

        $token = new BufferedOutput();
        $exitCode = $this->getApplication()?->find('phil:api-keys:create')->run(new ArrayInput($arguments), $token) ?? Command::FAILURE;
        if (Command::SUCCESS !== $exitCode) {
            $output->write($token->fetch());

            return $exitCode;
        }

        $output->writeln($this->seedBooks());
        $output->write($token->fetch());

        return Command::SUCCESS;
    }

    /**
     * Adds the example books when the database holds none.
     */
    private function seedBooks(): string
    {
        if ($this->books->count() > 0) {
            return 'The database already holds books: no example book added.';
        }

        foreach (self::BOOKS as [$title, $author, $publishedOn]) {
            $book = new Book();
            $book->setTitle($title);
            $book->setAuthor($author);
            $book->setPublishedOn(new DateTimeImmutable($publishedOn));
            $this->entityManager->persist($book);
        }

        $this->entityManager->flush();

        return \sprintf('%d example books added.', \count(self::BOOKS));
    }
}
