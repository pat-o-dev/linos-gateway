<?php

namespace App\Service\Prestashop;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class ProductImporter
{
    public function __construct(
        private ApiClient $client,
        private EntityManagerInterface $em
    ) {}

    public function import()
    {
        #TMP
        $source = 'PS0'; #uniq source
        $rootId = 2;
        $data = $this->client->getProducts();

        foreach ($data['products'] ?? [] as $row) {
            // try to get product sync
            $product = $this->em->getRepository(Product::class)->findOneBy([
                'source' => $source,
                'sourceId' => $row['id']
            ]) ?? new Product();

            $product->setSource($source);
            $product->setSourceId($row['id']);
            // id_category_default << for breadcrumb later
            $product->setManufacturerName($row['manufacturer_name']);
            $product->setSku($row['reference']);
            $product->setWeight($row['weight']);
            $product->setPrice($row['price']);
            $product->setTitle($row['name']);
            #TODO slug not unique
            $product->setSlug($row['link_rewrite']);
            $product->setShortDescription($row['description_short']);
            $product->setDescription($row['description']);

            // categories raw
            $sourceCategories = $row['associations']['categories'] ?? [];
            foreach ($sourceCategories as $sourceCategory) {
                if ((int) $sourceCategory['id'] !== $rootId) {
                    $category = $this->em->getRepository(Category::class)->findOneBy([
                        'source'   => $source,
                        'sourceId' => (int) $sourceCategory['id']
                    ]);
                    if ($category) {
                        $product->addCategory($category);
                    }
                }
            }
            $sourceImages = $row['associations']['images'] ?? [];
            //tmp just one
            $sourceImages = [$sourceImages[0]];
            foreach ($sourceImages as $sourceImage) {
                // use prestashop for media 
                $imageId = (int) $sourceImage['id'];
                $path = implode('/', str_split((string) $imageId));
                $image = "https://ps.linos.store/img/p/$path/$imageId-large_default.jpg";
                $product->setImage($image);
            }
            #TODO status
            $product->setStatus('PUBLISHED');
            $this->em->persist($product);
        }

        try {
            $this->em->flush();
            return true;
        } catch (\Throwable $e) {
            #TODO log & monitoring
            dd($e->getMessage());
            return false;
        }
    }

    public function check()
    {
        return true;
    }
}
