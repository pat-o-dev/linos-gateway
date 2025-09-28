<?php 

namespace App\Service\Prestashop;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryImporter
{
    public function __construct(
        private ApiClient $client,
        private EntityManagerInterface $em
    ) {}

    public function import() {
        #TMP
        $source = 'PS0';#uniq source
        $rootId = 2;#root presta

        $data = $this->client->getCategories();

         foreach($data['categories'] ?? [] as $row) {
            // exclude master & root
            if($row['id_parent'] == 0 || $row['id'] == $rootId) continue;
            // try to get category sync
            $category = $this->em->getRepository(Category::class)->findOneBy([
                'source' => $source,
                'sourceId' => $row['id']
            ]) ?? new Category();

            $category->setSource($source);
            $category->setSourceId($row['id']);
            #TODO parentId #TODO generate tree, we need parent is create for find him
            $category->setTitle($row['name']);
            #TODO slug not unique
            $category->setSlug($row['link_rewrite']);
            $category->setDescription($row['description']);
            $category->setPosition($row['position']);
            
            $this->em->persist($category);
        }

        try {
            $this->em->flush();
            return true;
        } catch (\Throwable $e) {
            #TODO log & monitoring
            return false;
        }

    }

    public function check() {
        return true;
    }
}
