<?php

namespace App\Service\Prestashop;

use App\Entity\SyncJob;
use App\Dto\CategoryDto;
use App\Message\JobMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CategoryImporter
{
    public function __construct(
        private ApiClient $client,
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
    ) {}

    public function import()
    {
        #TMP
        $source = 'PS0'; #uniq source
        $jobType = 'category_import';
        $origin = 'prestashop_api';
        $priority = 10;

        $data = $this->client->getCategories();
        $count = 0;
        foreach ($data['categories'] as $row) {
            if ($row['id_parent'] == 0) {
                continue;
            }
            if ($row['id_parent'] == 1) {
                $row['id_parent'] = 0;
            }
            $row['depth'] = (int) ($row['level_depth']);
            $row['slug'] = $row['link_rewrite'] ?? '';
            $row['parentId'] = $row['id_parent'] ?? '';
            $row['metaTitle'] = $row['meta_title'] ?? '';
            $row['metaDescription'] = $row['meta_description'] ?? '';
            $row['metaKeywords'] = $row['meta_keywords'] ?? '';
            $catDto = CategoryDto::fromArray($row);
      
            $job = new SyncJob(
                type: $jobType,
                objectId: $catDto->id,
                source: $source,
                origin: $origin,
                payload: $catDto->toArray(),
                priority: ($priority - $row['depth']),
            );

            $this->em->persist($job);
            $count++;
            if ($count % 10 === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        
        if($count > 0) {
            $this->em->flush();
            $this->bus->dispatch(new JobMessage($jobType));
        }
        

        return $count;
    }
}
