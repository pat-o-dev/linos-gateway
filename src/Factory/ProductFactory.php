<?php

namespace App\Factory;

use App\Entity\Product;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Product>
 */
final class ProductFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Product::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'sku' => self::faker()->unique()->ean13(),
            'slug' => self::faker()->unique()->slug(nbWords:3),
            'status' => self::faker()->randomElement(['DRAFT', 'PUBLISHED', 'PUBLISHED', 'PUBLISHED']),
            'title' => self::faker()->text(36),
            'description' => self::faker()->sentence(20),
            'image' => 'https://picsum.photos/400/400?random=' . self::faker()->unique()->numberBetween(1, 1000),
            'rating' => ['rate' => self::faker()->randomFloat(1, 1, 5), 'count' => self::faker()->numberBetween(1,300)],
            'price' => self::faker()->randomFloat(nbMaxDecimals:2, min: 5, max:300),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }
}
