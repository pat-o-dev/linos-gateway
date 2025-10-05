<?php

namespace App\Service\SyncJob;

use App\Entity\SyncJob;
use App\Repository\SyncJobRepository;
use App\Service\CategoryImporter;
use Doctrine\ORM\EntityManagerInterface;

class SyncJobProcessor
{
    private array $report = ['count' => 0, 'done' => 0, 'fail' => 0, 'skip' => 0, 'errors' => []];
    
    public function __construct(
        private EntityManagerInterface $em,
        private SyncJobRepository $syncJobRepository,
        private CategoryImporter $categoryImporter,
    ) {}

    public function initReport(): void 
    {
        $this->report = ['count' => 0, 'done' => 0, 'fail' => 0, 'skip' => 0, 'errors' => []];
    }

    public function process(array $criteria = [], int $limit = 10): array
    {
        $this->initReport();
        $jobs = $this->syncJobRepository->findAvailableJobs($criteria, $limit);
        if (!$jobs) {
            return $this->report;
        }
        foreach ($jobs as $job) {
            $this->report['count']++;
            $leaseUntil = (new \DateTimeImmutable())->modify('+5 minutes');
            if ($this->syncJobRepository->claimOpenJob($job->getId(), $leaseUntil)) {
                $job->markPending();
                $forceFlush = $this->processOneJob($job);
            } else {
                $this->report['skip']++;
            }
            
            if($this->report['count'] % 10 == 0 || $forceFlush) {
                $this->em->flush();
            }
        }

        $this->em->flush();

        return $this->report;
    }

    public function processOneJob(SyncJob $job): bool
    {
        $entity = false;   
        try { 
            switch ($job->getType()) {
                case 'category_import':
                    $entity = $this->categoryImporter->importJob($job);
                    break;
                default:
                    $job->addTry();
                    $this->report['fail']++;
                    $this->report['errors'][] = "Job {$job->getId()} failed: unknow type " . $job->getType();
                    return false;
            }
            if ($entity instanceof \App\Entity\Category) {
                $job->markDone();
                $this->report['done']++;
                $isNew = ($entity->getId() === null);
                $depth = $entity->getDepth();
                return ($isNew && $depth <= 2);// force flush if new entites and depth <= 2
            }

            $job->addTry();

            $this->report['fail']++;
            if (is_array($entity) && isset($entity['error'])) {
                $this->report['errors'][] = "Job {$job->getId()} failed: {$entity['error']}";
            } else {
                $this->report['errors'][] = "Unknown return type for job {$job->getId()}";
            }
        } catch (\Throwable $e) {
            $job->addTry();
            $this->report['fail']++;
            $this->report['errors'][] = "Job {$job->getId()} crashed: " . $e->getMessage();
        }
        return false;
    }
}
