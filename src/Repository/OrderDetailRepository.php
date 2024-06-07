<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository
 */
class OrderDetailRepository extends EntityRepository
{

    // public function findByCommandId($commandId): array
    // {
    //     return $this->createQueryBuilder('o')
    //         ->andWhere('o.command = :commandId')
    //         ->setParameter('commandId', $commandId)
    //         ->getQuery()
    //         ->getResult();
    // }

    //    /**
    //     * @return OrderDetail[] Returns an array of OrderDetail objects
    //     */
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




    //    public function findOneBySomeField($value): ?OrderDetail
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
