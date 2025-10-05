<?php

namespace App\Repository;

use App\Entity\SyncJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SyncJob>
 */
class SyncJobRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $em,
    )
    {
        parent::__construct($registry, SyncJob::class);
    }

    public function findAvailableJobs(array $criteria = [], int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('j')
            ->where('j.state = :state')
            ->andWhere('(j.availableAt IS NULL OR j.availableAt <= :now)')
            ->setParameter('state', 'open')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('j.priority', 'DESC')
            ->setMaxResults($limit);

        if (!empty($criteria['type'])) {
            $qb->andWhere('j.type = :type')->setParameter('type', $criteria['type']);
        }

        return $qb->getQuery()->getResult();
    }

    public function claimOpenJob(int $id, \DateTimeImmutable $leaseUntil): bool
    {
        return (bool) $this->em->createQuery(
            'UPDATE App\Entity\SyncJob j
            SET j.availableAt = :leaseUntil, j.updatedAt = :now
            WHERE j.id = :id AND j.state = :open
            AND (j.availableAt IS NULL OR j.availableAt <= :now)'
        )
        ->setParameters([
            'open'    => 'open',
            'now'     => new \DateTimeImmutable(),
            'leaseUntil' => $leaseUntil,
            'id'      => $id,
        ])
        ->execute();
    }

    public function hasOpenJobs(?string $type = null): bool
    {
        $qb = $this->createQueryBuilder('j')
            ->select('COUNT(j.id)')
            ->where('j.state = :state')
            ->andWhere('(j.availableAt IS NULL OR j.availableAt <= :now)')
            ->setParameter('state', 'open')
            ->setParameter('now', new \DateTimeImmutable());

        if($type) {
            $qb->andWhere('j.type = :type')
                ->setParameter('type', $type);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
