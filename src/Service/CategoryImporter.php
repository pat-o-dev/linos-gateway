<?php

namespace App\Service;

use App\Entity\SyncJob;
use App\Dto\CategoryDto;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CategoryImporter 
{

    public function __construct(
        private readonly EntityManagerInterface $em,
    ){
        
    }

    public function importJob(SyncJob $job): Category
    {
        $categoryDto = $job->getPayloadDto(CategoryDto::class);
        $source = $job->getSource();
        $sourceId = $job->getObjectId();
        return $this->import($categoryDto, $source, $sourceId);
    }

    public function import(CategoryDto $categoryDto, ?string $source = null, ?int $sourceId = null): Category
    {
        // try to get category sync
        $category = $this->em->getRepository(Category::class)->findOneBy([
            'source' => $source,
            'sourceId' => $sourceId
        ]) ?? new Category();
        
        if ($categoryDto->parentId > 0) {
            $categoryParent = $this->em->getRepository(Category::class)->findOneBy([
                'source' => $source,
                'sourceId' => $categoryDto->parentId
            ]) ?? null;
            if(!$categoryParent) {
                throw new \Exception("Parent not exist wait next try");
            }
            $category->setParent($categoryParent);
        } else {
            $category->setParent(null);
        }
        $category->setSource($source);
        $category->setSourceId($sourceId);
        $category->setTitle($categoryDto->name);
        $category->setSlug($categoryDto->slug);
        $category->setDescription($categoryDto->description);
        $category->setMetaTitle($categoryDto->metaTitle);
        $category->setMetaDescription($categoryDto->metaDescription);
        $category->setMetaKeywords($categoryDto->metaKeywords);
        $category->setDepth($categoryDto->lvl);

        $this->em->persist($category);

        try {
            $this->em->flush();
            return $category;
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }
}