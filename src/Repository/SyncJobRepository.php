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

    public function claimOpenJob(int $id): bool
    {
        return (bool) $this->em->createQuery(
            'UPDATE App\Entity\SyncJob j
            SET j.state = :pending, j.updatedAt = :now
            WHERE j.id = :id AND j.state = :open'
        )
        ->setParameters([
            'pending' => 'pending',
            'open'    => 'open',
            'now'     => new \DateTimeImmutable(),
            'id'      => $id,
        ])
        ->execute();
    }

    public function hasOpenJobs(?string $type = null): bool
    {
        $qb = $this->createQueryBuilder('j')
            ->select('COUNT(j.id)')
            ->where('j.state = :state')
            ->setParameter('state', 'open');

        if($type) {
            $qb->andWhere('j.type = :type')
                ->setParameter('type', $type);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
