<?php

namespace App\Service\Prestashop;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryImporter
{
    private array $categoriesBySourceId = [];

    public function __construct(
        private ApiClient $client,
        private EntityManagerInterface $em
    ) {}

    public function import()
    {
        #TMP
        $source = 'PS0'; #uniq source
        $rootId = 2; #root presta

        $data = $this->client->getCategories();

        foreach ($data['categories'] ?? [] as $row) {
            // exclude master & root
            if ($row['id_parent'] == 0 || $row['id'] == $rootId) continue;
            // try to get category sync
            $category = $this->em->getRepository(Category::class)->findOneBy([
                'source' => $source,
                'sourceId' => $row['id']
            ]) ?? new Category();

            $category->setSource($source);
            $category->setSourceId($row['id']);
            if ((int) $row['id_parent'] > 0 && (int) $row['id_parent'] !== $rootId) {
                $key_parent = $source . '-' . (int) $row['id_parent'];
                $parent = $this->categoriesBySourceId[$key_parent] ?? null;
                $category->setParent($parent);
            } else {
                $category->setParent(null);
            }
            $category->setTitle($row['name']);
            #TODO slug not unique
            $category->setSlug($row['link_rewrite']);
            $category->setDescription($row['description']);
            $category->setPosition($row['position']);

            $this->em->persist($category);
            $key = $source . '-' . $row['id'];
            $this->categoriesBySourceId[$key] = $category;
        }

        try {
            $this->em->flush();
            return true;
        } catch (\Throwable $e) {
            #TODO log & monitoring
            return false;
        }
    }

    public function check()
    {
        return true;
    }
}
