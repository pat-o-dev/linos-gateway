<?php

namespace App\Story;

use Zenstruck\Foundry\Story;
use App\Factory\ProductFactory;
use App\Factory\CategoryFactory;
use Zenstruck\Foundry\Attribute\AsFixture;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        CategoryFactory::createMany(10);
        //ProductFactory::createMany(100);

        ProductFactory::new()
            ->many(100)
            ->create(function() {
                return [
                    'category' => CategoryFactory::randomRange(0, 5)
                ];
            });
    }
}
