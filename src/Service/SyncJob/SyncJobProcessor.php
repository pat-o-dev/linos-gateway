<?php

namespace App\Service\SyncJob;

use App\Repository\SyncJobRepository;
use App\Service\CategoryImporter;
use Doctrine\ORM\EntityManagerInterface;

class SyncJobProcessor
{
    public function __construct(
        private EntityManagerInterface $em,
        private SyncJobRepository $syncJobRepository,
        private CategoryImporter $categoryImporter,
    ) {}

    public function process(array $criteria = [], int $limit = 10): array
    {
        $jobs = $this->syncJobRepository->findAvailableJobs($criteria, $limit);
        $report = ['count' => 0, 'done' => 0, 'skip' => 0, 'fail' => 0, 'errors' => []];
        if (!$jobs) {
            return $report;
        }
        foreach ($jobs as $job) {
            $report['count']++;
            $claimed = $this->syncJobRepository->claimOpenJob($job->getId());
            if (!$claimed) {
                $report['skip']++;
                continue;
            }
            try {
                $execute = false;
                switch ($job->getType()) {
                    case 'category_import':
                        $execute = $this->categoryImporter->importJob($job);
                        break;
                    default:
                        $job->markError();
                        $report['fail']++;
                        $report['errors'][] = "Job {$job->getId()} failed: unknow type " . $job->getType();
                        continue 2;
                }
                if ($execute !== false) {
                    $report['done']++;
                    $job->markDone();
                } else {
                    $job->addTry();
                    $report['fail']++;
                    $report['errors'][] = "Job {$job->getId()} failed: execute ";
                }
            } catch (\Throwable $e) {
                $job->addTry();
                $report['fail']++;
                $report['errors'][] = "Job {$job->getId()} failed: " . $e->getMessage();
            }

            if (($report['count'] % 10) === 0) {
                $this->em->flush();
            }
        }
        $this->em->flush();

        return $report;
    }
}
