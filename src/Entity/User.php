<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\{ApiFilter, ApiResource, Get, Post, Put, GetCollection};
use Doctrine\{
    Common\Collections\ArrayCollection,
    Common\Collections\Collection,
    DBAL\Types\Types,
    ORM\Mapping as ORM
};
use Symfony\Component\{
    Security\Core\User\PasswordAuthenticatedUserInterface,
    Security\Core\User\UserInterface,
    Serializer\Annotation\Groups,
    Validator\Constraints as Assert
};
use App\{
    Controller\User\ActivateAccountController,
    Controller\User\ChangePasswordController,
    Controller\User\CreateAccountController,
    Controller\User\ResetPasswordController,
    Enum\RoleEnum,
    Repository\UserRepository
};
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['read']
            ],
            security: "is_granted('IS_AUTHENTICATED_FULLY')"
        ),
        new Post(
            uriTemplate: '/users/create-account',
            controller: CreateAccountController::class,
            security: "is_granted('". RoleEnum::ROLE_USER->value ."')",
            name: 'create_account',
        ),
        new Post(
            uriTemplate: '/users/reset-password',
            controller: ResetPasswordController::class,
            deserialize: false,
            name: 'reset_password',
        ),
        new Put(
            uriTemplate: '/users/active-account/{id}',
            controller: ActivateAccountController::class,
            name: 'active_account',
        ),
        new Put(
            uriTemplate: '/users/change-password/{id}',
            controller: ChangePasswordController::class,
            name: 'change_password',
        ),
        new Put(
            normalizationContext: [
                'groups' => ['read']
            ],
            denormalizationContext: [
                'groups' => ['put']
            ],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object == user",
        ),
        new GetCollection(),
        new Post(
            denormalizationContext: [
                'groups' => ['post']
            ],
        ),
    ],
    paginationItemsPerPage: 10,
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['username', 'email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 32)]
    #[Groups(['read', 'post'])]
    private string $username;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true)]
    #[Assert\NotNull]
    #[Assert\Email]
    #[Groups(['post'])]
    private string $email;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column]
    #[Groups(['put', 'post'])]
    #[Assert\Regex(
        pattern: "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}/",
        message: 'The password must be eight characters long and contain at least one number, one upper case letter and one lower case letter.',
    )]
    #[Assert\NotNull]
    private ?string $password;

    #[ORM\Column]
    #[Groups(['put', 'post'])]
    #[Assert\Expression(
        "this.getPassword() === this.getRepeatPassword()",
        message: "Password is not the same"
    )]
    #[Assert\NotNull]
    private string $repeatPassword;

    #[ORM\Column(nullable: true)]
    private ?string $plainPassword = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'put', 'post'])]
    private ?string $logo;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[ApiFilter(BooleanFilter::class)]
    #[Groups(['put', 'post'])]
    private bool $enabled;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $token;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $resetPasswordToken;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $lastPasswordChange = null;

    #[ORM\OneToMany('owner', Comment::class)]
    #[Groups(['read'])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
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

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array(needle: RoleEnum::ROLE_USER->value, haystack: $roles, strict: true)) {
            $roles[] = RoleEnum::ROLE_USER->value;
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRepeatPassword(): string
    {
        return $this->repeatPassword;
    }

    public function setRepeatPassword(string $repeatPassword): self
    {
        $this->repeatPassword = $repeatPassword;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): void
    {
        $this->resetPasswordToken = $resetPasswordToken;
    }

    public function getLastPasswordChange(): ?DateTimeImmutable
    {
        return $this->lastPasswordChange;
    }

    public function setLastPasswordChange(DateTimeImmutable $lastPasswordChange): static
    {
        $this->lastPasswordChange = $lastPasswordChange;

        return $this;
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
            $comment->setOwner($this);
        }
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment) && $comment->getOwner() === $this) {
            $comment->setOwner(null);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
