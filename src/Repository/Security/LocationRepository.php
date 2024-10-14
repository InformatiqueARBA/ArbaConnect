<?php

namespace App\Repository\Security;

use App\Entity\Security\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Location::class);
        $this->entityManager = $entityManager;
    }

    public function findByLocation(string $value): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.location = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function findWarehouseByInventoryNumber(string $inventoryNumber): ?string
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l.warehouse')
            ->where('l.inventoryNumber = :inventoryNumber')
            ->setParameter('inventoryNumber', $inventoryNumber)
            ->setMaxResults(1)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result ? $result['warehouse'] : null;
    }

    // public function findByInventoryNumber(string $inventoryNumber): array
    // {
    //     return $this->createQueryBuilder('l')
    //         ->where('l.inventoryNumber = :inventoryNumber')
    //         ->setParameter('inventoryNumber', $inventoryNumber)
    //         ->getQuery()
    //         ->getResult();
    // }
}
