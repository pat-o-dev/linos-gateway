<?php

namespace App\Dto;

class ProductDto
{
    public function __construct(
        public int $id,
        public ?\DateTimeInterface $createdAt = null,
        public ?\DateTimeInterface $updatedAt = null,
        public string $sku,
        public string $name,
        public ?string $slug,
        public ?float $price = 0,
        public ?string $shortDescription = null,
        public ?string $description = null,
        public ?string $metaTitle = null,
        public ?string $metaDescription = null,
        public ?string $metaKeywords = null,
        public ?int $stock = 0,
        public ?float $weight = 0,
        public bool $isBundle = false,
        public bool $isVariant = false,
        public ?int $parentId = null,
        /** @var CategoryRefDto[] */
        public array $categories = [],
        /** @var MediaDto[] */
        public array $medias = [],
        /** @var AttributeDto[] */
        public array $attributes = [],
        /** @var BundleItemDto[] */
        public array $bundleItems = []
    ) {}
}