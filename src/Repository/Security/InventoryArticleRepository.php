<?php

namespace App\Repository\Security;

use App\Entity\Security\InventoryArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventoryArticle>
 */
class InventoryArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryArticle::class);
    }


    public function findByLocationAndWarehouse(string $warehouse, string $location): array
    {

        return $this->createQueryBuilder('i')
            ->where('(SUBSTRING(i.location, 1, 5) = :val2 OR SUBSTRING(i.location2, 1, 5) = :val2 OR SUBSTRING(i.location3, 1, 5) = :val2)')
            ->andWhere('i.warehouse = :val')
            ->setParameter('val', $warehouse)
            ->setParameter('val2', $location)
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return InventoryArticle[] Returns an array of InventoryArticle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?InventoryArticle
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
