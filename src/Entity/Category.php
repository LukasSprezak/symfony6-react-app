<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource]
class Category
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->setCreatedAt(new DateTimeImmutable());
        $this->setUpdatedAt(new DateTimeImmutable());
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getProducts(): Collection
    {
        return new ArrayCollection($this->products->toArray());
    }

    public function containsProducts(Product $product): bool
    {
        return $this->products->contains($product);
    }

    public function addProduct(Product $product): void
    {
        if (!$this->containsProducts($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }
    }

    public function removeProduct(Product $product): void
    {
        if (!$this->containsProducts($product)) {
            $this->products->removeElement($product);
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
