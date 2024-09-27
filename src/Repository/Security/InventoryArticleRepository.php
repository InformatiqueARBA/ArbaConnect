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


    public function findByLocationOrLocation2OrLocation3(string $value): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.location = :val')
            ->orWhere('i.location2 = :val')
            ->orWhere('i.location3 = :val')
            ->setParameter('val', $value)
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
