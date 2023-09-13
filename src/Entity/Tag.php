<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\{
    DBAL\Types\Types,
    ORM\Mapping as ORM
};
use ApiPlatform\Metadata\ApiResource;
use App\Repository\TagRepository;
use JsonSerializable;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ApiResource]
class Tag implements JsonSerializable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function jsonSerialize(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
