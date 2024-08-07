<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository
 */
class OrderRepository extends EntityRepository
{

    public function findByCorporationId(string $corporationId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.corporation = :corpId')
            ->setParameter('corpId', $corporationId)
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
