<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\{
    ApiResource,
    Get,
    Post,
    Put,
    GetCollection
};
use Doctrine\{
    DBAL\Types\Types,
    ORM\Mapping as ORM
};
use Symfony\Component\{
    Security\Core\User\UserInterface,
    Serializer\Annotation\Groups,
    Validator\Constraints as Assert
};
use DateTimeImmutable;
use Gedmo\Mapping\Annotation\Timestampable;
use App\Repository\CommentRepository;

#[ApiResource(
    operations: [
        new Get(),
        new Put(
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object.getUser() == user",
        ),
        new GetCollection(),
        new Post(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
    ],
    denormalizationContext: [
        'groups' => ['post']
    ],
)
]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment implements OwnerInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'comment.blank')]
    #[Assert\Length(
        min: 5,
        max: 10000,
        minMessage: 'comment.too_short',
        maxMessage: 'comment.too_long'
    )]
    #[Groups(['post'])]
    private ?string $content;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Timestampable(on: 'create')]
    private DateTimeImmutable $publishedAt;

    public function __construct()
    {
        $this->publishedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?UserInterface $owner): OwnerInterface
    {
        $this->owner = $owner;
        return $this;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
