<?php

namespace App\Service;

use Exception;
use App\Entity\SyncJob;
use App\Dto\CategoryDto;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class CategoryImporter 
{

    public function __construct(
        private EntityManagerInterface $em,
    ){
        
    }

    public function importJob(SyncJob $job): Category|array
    {
        $categoryDto = $job->getPayloadDto(CategoryDto::class);
        $source = $job->getSource();
        $sourceId = $job->getObjectId();
        return $this->import($categoryDto, $source, $sourceId);
    }

    public function import(CategoryDto $categoryDto, ?string $source = null, ?int $sourceId = null): Category|array
    {
        // try to get category sync
        $category = $this->em->getRepository(Category::class)->findOneBy([
            'source' => $source,
            'sourceId' => $sourceId
        ]) ?? new Category();
        
        if ($categoryDto->parentId > 0) {
            $parentCategory = $this->em->getRepository(Category::class)->findOneBy([
                'source' => $source,
                'sourceId' => $categoryDto->parentId
            ]) ?? null;
            if(!$parentCategory) {
                return ['error' => "{$sourceId } : Parent {$categoryDto->parentId} not exist wait next try"];
            }
            $category->setParent($parentCategory);
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
        $category->setDepth($categoryDto->depth);

        $this->em->persist($category);

        return $category;
    }
}