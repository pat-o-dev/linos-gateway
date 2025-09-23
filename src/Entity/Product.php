<?php

namespace App\Entity;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:list', 'product:item', 'category:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:list', 'product:item', 'category:list'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:list', 'product:item', 'category:list'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:list', 'product:item', 'category:list'])]
    private ?string $sku = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['product:list', 'product:item'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['product:list', 'product:item', 'category:list'])]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['product:list', 'product:item', 'category:list'])]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['product:list', 'product:item', 'category:list'])]
    private ?array $rating = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[Groups(['product:list', 'product:item'])]
    private Collection $categories;

    #[ORM\Column(length: 10)]
    #[Groups(['product:list', 'product:item', 'category:list'])]
    private ?string $status = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getRating(): ?array
    {
        return $this->rating;
    }

    public function setRating(?array $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
