<?php

namespace App\Dto;

class ProductPackItemDto
{
    public function __construct(
        public int $productId,
        public int $quantity,
        public float $price,
        public int $position,
        public bool $required
    ) {}
}