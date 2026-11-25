<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\BookRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A book of the example API: everyone reads the books, a token with the scope "books:write" creates, changes and
 * deletes them.
 *
 * @see BookApiTest
 */
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: self::WRITE),
        new Patch(security: self::WRITE),
        new Delete(security: self::WRITE),
    ],
    normalizationContext: ['groups' => [self::READ_GROUP]],
    denormalizationContext: ['groups' => [self::WRITE_GROUP]],
    order: ['title' => 'ASC'],
)]
final class Book
{
    /**
     * The expression of the operations that write: the voter of phil/api-keys-bundle checks the scope of the token.
     */
    public const string WRITE = "is_granted('API_SCOPE', 'books:write')";

    public const string READ_GROUP = 'book:read';

    public const string WRITE_GROUP = 'book:write';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[Groups([self::READ_GROUP])]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    private string $title = '';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    private string $author = '';

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups([self::READ_GROUP, self::WRITE_GROUP])]
    private ?DateTimeImmutable $publishedOn = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getPublishedOn(): ?DateTimeImmutable
    {
        return $this->publishedOn;
    }

    public function setPublishedOn(?DateTimeImmutable $publishedOn): void
    {
        $this->publishedOn = $publishedOn;
    }
}
