<?php

namespace App\Entity;

use ORM\UniqueConstraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueConstraint(name: "uniq_source_source_id", columns: ["source", "source_id"])]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:list', 'category:item', 'product:list', 'product:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "children")]
    private ?Category $parent = null;

    #[ORM\OneToMany(mappedBy: "parent", targetEntity: Category::class)]
    #[Groups(['category:tree', 'category:item'])]
    private Collection $children;

    #[ORM\Column(nullable: true)]
    #[Groups(['category:tree'])]
    private ?int $depth = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['category:tree'])]
    private ?int $position = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(nullable: true)]
    private ?int $sourceId = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:list', 'category:item', 'product:list', 'product:item'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['category:list', 'category:item', 'product:list', 'product:item'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['category:item'])]
    private ?string $description = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'categories')]
    #[Groups(['category:list'])]
    private Collection $products;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getDepth(): ?int
    {
        return $this->depth;
    }

    public function setDepth(int $depth)
    {
        $this->depth = $depth;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getSourceId(): ?int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): static
    {
        $this->sourceId = $sourceId;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function setProducts(Collection $products): static
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Category $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
    }

    public function removeChild(Category $child): self
    {
        if ($this->children->removeElement($child)) {
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            $product->removeCategory($this);
        }

        return $this;
    }
}
