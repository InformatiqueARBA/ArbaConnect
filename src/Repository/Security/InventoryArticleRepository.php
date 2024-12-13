<?php

namespace App\Repository\Security;

use App\Entity\Security\InventoryArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class InventoryArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryArticle::class);
    }




    // Retourne les articles pour la vue & l'impression PDF par allée stockée
    // public function findByLocationAndWarehouseAndArtType(string $inventoryNumber, string $warehouse, string $location): array
    // {

    //     return $this->createQueryBuilder('i')
    //         ->where('(SUBSTRING(i.location, 1, 5) = :val3)')
    //         ->andWhere('i.inventoryNumber = :val')
    //         ->andWhere('i.warehouse = :val2')
    //         ->andWhere('i.typeArticle = :typeArticle')
    //         ->setParameter('val', $inventoryNumber)
    //         ->setParameter('val2', $warehouse)
    //         ->setParameter('val3', $location)
    //         ->setParameter('typeArticle', 'ART')
    //         ->orderBy('i.location', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    // }
    public function findByLocationAndWarehouseAndArtType(string $inventoryNumber, string $warehouse, string $location): array
    {
        return $this->createQueryBuilder('i')
            ->where('SUBSTRING(i.location, 1, 5) = :val3')
            ->andWhere('i.inventoryNumber = :val')
            ->andWhere('i.warehouse = :val2')
            ->andWhere('i.typeArticle = :typeArticle')
            ->andWhere('i.servedFromStock = :servedFromStock')
            ->setParameter('val', $inventoryNumber)
            ->setParameter('val2', $warehouse)
            ->setParameter('val3', $location)
            ->setParameter('typeArticle', 'ART')
            ->setParameter('servedFromStock', 'OUI')
            ->orderBy('i.location', 'ASC')
            ->getQuery()
            ->getResult();
    }





    // Retourne les articles pour la vue & l'impression PDF par allée lot
    // public function findByLocationAndWarehouseAndLovType(string $inventoryNumber, string $warehouse, string $location): array
    // {

    //     return $this->createQueryBuilder('i')
    //         ->where('(SUBSTRING(i.location, 1, 5) = :val3)')
    //         ->andWhere('i.inventoryNumber = :val')
    //         ->andWhere('i.warehouse = :val2')
    //         ->andWhere('i.typeArticle = :typeArticle')
    //         ->setParameter('val', $inventoryNumber)
    //         ->setParameter('val2', $warehouse)
    //         ->setParameter('val3', $location)
    //         ->setParameter('typeArticle', 'LOV')
    //         ->orderBy('i.location', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    // }

    public function findByLocationAndWarehouseAndLovType(string $inventoryNumber, string $warehouse, string $location): array
    {
        return $this->createQueryBuilder('i')
            ->where('SUBSTRING(i.location, 1, 5) = :val3')
            ->andWhere('i.inventoryNumber = :val')
            ->andWhere('i.warehouse = :val2')
            ->andWhere('i.typeArticle = :typeArticle')
            ->andWhere('i.servedFromStock = :servedFromStock')
            ->setParameter('val', $inventoryNumber)
            ->setParameter('val2', $warehouse)
            ->setParameter('val3', $location)
            ->setParameter('typeArticle', 'LOV')
            ->setParameter('servedFromStock', 'OUI')
            ->orderBy('i.location', 'ASC')
            ->getQuery()
            ->getResult();
    }





    // Retourne les articles pour la vue des non référencés
    public function findByUnknownArticleTag(): array
    {

        return $this->createQueryBuilder('i')
            ->where('i.unknownArticle = :val')
            ->setParameter('val', '1')
            ->orderBy('i.location', 'ASC')
            ->getQuery()
            ->getResult();
    }




    // Retourne les articles par inventaire pour les stockés sur les CSV
    public function findByInventoryNumberAndWarehouseAndArtType(string $inventoryNumber, string $warehouse): array
    {

        return $this->createQueryBuilder('i')
            ->where('i.inventoryNumber = :val')
            ->andWhere('i.warehouse = :val2')
            ->andWhere('i.typeArticle = :typeArticle')
            ->setParameter('val', $inventoryNumber)
            ->setParameter('val2', $warehouse)
            ->setParameter('typeArticle', 'ART')
            ->orderBy('i.location', 'ASC')
            ->getQuery()
            ->getResult();
    }




    // Retourne les articles par inventaire pour les lots sur les CSV
    public function findByInventoryNumberAndWarehouseAndLovType(string $inventoryNumber, string $warehouse): array
    {

        return $this->createQueryBuilder('i')
            ->where('i.inventoryNumber = :val')
            ->andWhere('i.warehouse = :val2')
            ->andWhere('i.typeArticle = :typeArticle')
            ->setParameter('val', $inventoryNumber)
            ->setParameter('val2', $warehouse)
            ->setParameter('typeArticle', 'LOV')
            ->orderBy('i.location', 'ASC')
            ->getQuery()
            ->getResult();
    }





    // retourner tous les articles (codesArticle) et leurs localisations associées en fonction d'un codesArticle
    public function findArticleCodeWithLocations(string $articleCode): array
    {
        return $this->createQueryBuilder('i')
            // ->select('i.articleCode, i.location, i.location2, i.location3, i.quantityLocation1')
            ->select('i.articleCode, i.location, i.quantityLocation1,i.totalQuantity')
            ->where('i.articleCode = :articleCode')
            ->setParameter('articleCode', $articleCode)
            ->orderBy('i.articleCode', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findArticleCodeWithLocations2(string $articleCode): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.articleCode = :articleCode')
            ->setParameter('articleCode', $articleCode)
            ->orderBy('i.articleCode', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
