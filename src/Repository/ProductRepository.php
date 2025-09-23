<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function findAllPaginated(int $page = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('p')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query, true);
        
        return $paginator;
    }

    public function findByCategoryPaginated(Category $category, int $page = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->where('c = :category')
            ->setParameter('category', $category)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query, false);
        
        return $paginator;
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
