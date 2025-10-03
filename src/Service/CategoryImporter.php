<?php

namespace App\Service;

use App\Dto\CategoryDto;
use App\Entity\SyncJob;

class CategoryImporter 
{

    public function __construct(

    ){
        
    }

    public function importJob(SyncJob $job): bool
    {
        $categoryDto = $job->getPayloadDto(CategoryDto::class);
        return self::import($categoryDto);
    }

    public function import(CategoryDto $categoryDto): bool
    {
        
        
        return true;
    }
}