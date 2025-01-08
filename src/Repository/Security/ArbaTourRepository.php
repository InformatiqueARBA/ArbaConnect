<?php

namespace App\Repository\Security;

use App\Entity\Security\ArbaTour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArbaTour>
 */
class ArbaTourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArbaTour::class);
    }



    public function findTourCodeByZipCode(string $zipCode): ?string
    {
        try {
            return $this->createQueryBuilder('a')
                ->select('a.TourCode')
                ->where('a.zipCode = :zipCode')
                ->setParameter('zipCode', $zipCode)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException | \Doctrine\ORM\NonUniqueResultException $e) {
            return null;
        }
    }



    //    /**
    //     * @return ArbaTour[] Returns an array of ArbaTour objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ArbaTour
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
