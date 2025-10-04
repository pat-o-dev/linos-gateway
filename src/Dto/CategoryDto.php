<?php

namespace App\Dto;

class CategoryDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public ?int $parentId = 0,
        public ?int $lvl = 1,
        public ?string $description = null,
        public ?\DateTimeInterface $createdAt = null,
        public ?\DateTimeInterface $updatedAt = null,
        public ?string $metaTitle = null,
        public ?string $metaKeywords = null,
        public ?string $metaDescription = null,
        public ?string $image = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            parentId: !empty($data['parentId'] ?? null) ? (int) $data['parentId'] : null,
            lvl: !empty($data['lvl'] ?? null) ? (int) $data['lvl'] : null,
            description: $data['description'] ?? null,
            createdAt: !empty($data['createdAt'] ?? null) ? new \DateTime($data['createdAt']) : null,
            updatedAt: !empty($data['updatedAt'] ?? null) ? new \DateTime($data['updatedAt']) : null,
            metaTitle: $data['metaTitle'] ?? null,
            metaKeywords: $data['metaKeywords'] ?? null,
            metaDescription: $data['metaDescription'] ?? null,
            image: $data['image'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parentId' => $this->parentId,
            'lvl' => $this->lvl,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'metaTitle' => $this->metaTitle,
            'metaKeywords' => $this->metaKeywords,
            'metaDescription' => $this->metaDescription,
            'image' => $this->image,
        ];
    }
}