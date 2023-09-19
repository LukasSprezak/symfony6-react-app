<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\{ApiResource, Delete, Get, Post, Put, GetCollection};
use Doctrine\{Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    DBAL\Types\Types,
    ORM\Mapping as ORM,
};
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\{Security\Core\User\UserInterface,
    Serializer\Annotation\Groups,
    String\AbstractString,
    String\UnicodeString,
    Validator\Constraints as Assert
};
use App\{
    Enum\StatusProductEnum,
    Repository\ProductRepository
};
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTimeImmutable;
use function Symfony\Component\String\u;

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
        new Delete(),
    ],
    denormalizationContext: [
        'groups' => ['post']
    ],
)
]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity(fields: ['slug'])]
class Product implements OwnerInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 32)]
    #[Groups(['post'])]
    private ?string $name = null;

    #[ORM\OneToMany(
        mappedBy: 'product',
        targetEntity: Comment::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Tag::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'product_tag')]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(
        name: 'category_id',
        referencedColumnName: 'id',
        onDelete: 'SET NULL'
    )]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner;

    #[ORM\Column(type: Types::STRING, length: 20, enumType: StatusProductEnum::class)]
    #[Groups(['post'])]
    private StatusProductEnum $status;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['post'])]
    private string $description;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotNull]
    #[Groups(['post'])]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->setCreatedAt(new DateTimeImmutable());
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getComments(): Collection
    {
        return new ArrayCollection($this->comments->toArray());
    }

    public function containsComments(Comment $comment): bool
    {
        return $this->comments->contains($comment);
    }

    public function addComment(Comment $comment): void
    {
        if (!$this->containsComments($comment)) {
            $this->comments->add($comment);
            $comment->setProduct($this);
        }
    }

    public function removeComment(Comment $comment): void
    {
        if (!$this->containsComments($comment)) {
            $this->comments->removeElement($comment);
        }
    }

    public function getTags(): Collection
    {
        return new ArrayCollection($this->tags->toArray());
    }

    public function containsTags(Tag $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function addTag(Tag ...$tags): void
    {
        foreach ($tags as $tag) {
            if (!$this->containsTags($tag)) {
                $this->tags->add($tag);
            }
        }
    }

    public function removeTag(Tag $tag): void
    {
        if (!$this->containsTags($tag)) {
            $this->tags->removeElement($tag);
        }
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category = null): self
    {
        $this->category = $category;

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

    public function getStatus(): StatusProductEnum
    {
        return $this->status;
    }

    public function setStatus(StatusProductEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getShortDescription(): AbstractString|UnicodeString
    {
        return u($this->getDescription())->truncate(length: 40, ellipsis: '...');
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
