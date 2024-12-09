<?php

namespace App\Repository\Security;

use App\Entity\Security\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class LocationRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Location::class);
        $this->entityManager = $entityManager;
    }




    // Permet de récupérer la localisation dont le status est à verrouiller
    public function findByLocation(string $value): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.location = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }




    // Retourne le dépôt en fonction du numéro d'inventaire
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



    public function findLocationsWithLovArticles(): array
    {
        $qb = $this->createQueryBuilder('l');

        return $qb->join('App\Entity\Security\InventoryArticle', 'ia', 'WITH', 'l.inventoryNumber = ia.inventoryNumber')
            ->where('ia.typeArticle = :typeArticle')
            ->andWhere('SUBSTRING(ia.location, 1, 5) = l.location')
            ->andWhere($qb->expr()->isNotNull('ia.articleCode')) // Vérifie que le code article n'est pas null
            ->andWhere($qb->expr()->neq('ia.articleCode', "''"))  // Vérifie que le code article n'est pas vide
            ->andWhere('ia.servedFromStock = :servedFromStock') // Vérifie que servedFromStock = 'oui'
            ->setParameter('typeArticle', 'LOV')
            ->setParameter('servedFromStock', 'oui')
            ->orderBy('l.warehouse', 'ASC') // Ordre par entrepôt (warehouse) en premier
            ->addOrderBy('l.location', 'ASC') // Puis ordre par emplacement (location)
            ->getQuery()
            ->getResult();
    }



    public function findLocationsWithArtArticles(): array
    {
        $qb = $this->createQueryBuilder('l');

        return $qb->join('App\Entity\Security\InventoryArticle', 'ia', 'WITH', 'l.inventoryNumber = ia.inventoryNumber')
            ->where('ia.typeArticle = :typeArticle')
            ->andWhere('SUBSTRING(ia.location, 1, 5) = l.location')
            ->andWhere($qb->expr()->isNotNull('ia.articleCode')) // Vérifie que le code article n'est pas null
            ->andWhere($qb->expr()->neq('ia.articleCode', "''"))  // Vérifie que le code article n'est pas vide
            ->andWhere('ia.servedFromStock = :servedFromStock') // Vérifie que servedFromStock = 'oui'
            ->setParameter('typeArticle', 'ART')
            ->setParameter('servedFromStock', 'oui')
            ->orderBy('l.warehouse', 'ASC') // Ordre par entrepôt (warehouse) en premier
            ->addOrderBy('l.location', 'ASC') // Puis ordre par emplacement (location)
            ->getQuery()
            ->getResult();
    }




    // Retourne les allées ayant des articles lot associés pour un inventaire donné
    public function findLocationsWithLovArticlesByinventoryNumber(string $inventoryNumber): array
    {
        return $this->createQueryBuilder('l')
            ->join('App\Entity\Security\InventoryArticle', 'ia', 'WITH', 'l.inventoryNumber = ia.inventoryNumber')
            ->where('ia.typeArticle = :typeArticle')
            ->andWhere('SUBSTRING(ia.location, 1, 5) = l.location')
            ->andwhere('l.inventoryNumber = :inventoryNumber')
            ->andWhere('ia.servedFromStock = :servedFromStock') // Vérifie que servedFromStock = 'oui'
            ->setParameter('inventoryNumber', $inventoryNumber)
            ->setParameter('servedFromStock', 'oui')
            ->setParameter('typeArticle', 'LOV')
            ->orderBy('l.warehouse', 'ASC') // Ordre par entrepôt (warehouse) en premier
            ->addOrderBy('l.location', 'ASC') // Puis ordre par emplacement (location)
            ->getQuery()
            ->getResult();
    }






    // Retourne les allées ayant des articles stockés associés pour un inventaire donné
    public function findLocationsWithArtArticlesByinventoryNumber(string $inventoryNumber): array
    {
        return $this->createQueryBuilder('l')
            ->join('App\Entity\Security\InventoryArticle', 'ia', 'WITH', 'l.inventoryNumber = ia.inventoryNumber')
            ->where('ia.typeArticle = :typeArticle')
            ->andWhere('SUBSTRING(ia.location, 1, 5) = l.location')
            ->andwhere('l.inventoryNumber = :inventoryNumber')
            ->andWhere('ia.servedFromStock = :servedFromStock') // Vérifie que servedFromStock = 'oui'
            ->setParameter('inventoryNumber', $inventoryNumber)
            ->setParameter('servedFromStock', 'oui')
            ->setParameter('typeArticle', 'ART')
            ->orderBy('l.warehouse', 'ASC') // Ordre par entrepôt (warehouse) en premier
            ->addOrderBy('l.location', 'ASC') // Puis ordre par emplacement (location)
            ->getQuery()
            ->getResult();
    }
}
