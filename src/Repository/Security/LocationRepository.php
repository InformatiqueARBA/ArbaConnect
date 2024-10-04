<?php

namespace App\Repository\Security;

use App\Entity\Security\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
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

        // Exécuter la requête et récupérer le tableau
        $result = $qb->getOneOrNullResult();


        // Retourner la valeur de 'warehouse' ou null si aucun résultat n'est trouvé
        return $result ? $result['warehouse'] : null;
    }

    public function findByInventoryNumber(string $inventoryNumber): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.inventoryNumber = :inventoryNumber') // Filtrer par le numéro d'inventaire
            ->setParameter('inventoryNumber', $inventoryNumber) // Assigner le paramètre à la requête
            ->getQuery()
            ->getResult(); // Retourner un tableau avec tous les résultats
    }



    //    /**
    //     * @return Location[] Returns an array of Location objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Location
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
