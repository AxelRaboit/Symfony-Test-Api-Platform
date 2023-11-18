<?php

namespace App\Entity;

use DateTimeImmutable;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
            normalizationContext: ['groups' => ['read:Article:collection', 'read:Article:item', 'read:Article']]),
        new Put(
            /* We need to define the validation context for the PUT operation
            because the validation context defined for Get will be used otherwise. */
            validationContext: ['groups' => ['update:Article']]
        ),
        new Delete(),
        new Post(
            /* validationContext: ['groups' => ['create:Article']] */ // Quickly way to define the validation context
            validationContext: ['groups' => [Article::class, 'validationGroupsCreate']]
        ),
        new Patch(),
    ],
    normalizationContext: ['groups' => ['read:Article:collection']],
    denormalizationContext: ['groups' => ['write:Article']],
    validationContext: ['groups' => ['create:Article']],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 2,
    paginationMaximumItemsPerPage: 2
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'title' => 'partial',
])]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Article:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Groups(['read:Article:collection', 'write:Article']),
        Length(min: 5, max: 255, groups: ['create:Article']),
    ]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[
        Groups(['read:Article:collection', 'write:Article']),
        Length(min: 5, max: 255),
    ]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:Article:item', 'write:Article'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['read:Article:item', 'write:Article'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:Article:item', 'write:Article'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'Articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[
        Groups(['read:Article:item', 'write:Article']),
        Valid(),
    ]
    private ?Category $category = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    // Validation groups

    public static function validationGroups(self $Article, string $method): array
    {
        if ($method === 'POST') {
            return ['create:Article'];
        }

        return ['update:Article'];
    }

    public static function validationGroupsCreate(self $Article): array
    {
        return ['create:Article'];
    }
}
