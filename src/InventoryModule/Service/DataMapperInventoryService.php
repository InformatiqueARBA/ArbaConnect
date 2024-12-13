<?php

namespace App\InventoryModule\Service;

use App\ArbaConnect\Service\OdbcService;
use App\Entity\Security\InventoryArticle;
use App\Entity\Security\Location;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\BrowserKit\Response;

class DataMapperInventoryService
{

    private $em;
    private $odbcService;
    private $requestOdbcInventoryService;

    public function __construct(OdbcService $odbcService, ManagerRegistry $managerRegistry, RequestOdbcInventoryService $requestOdbcInventoryService)
    {
        $this->em = $managerRegistry->getManager('security');
        $this->odbcService = $odbcService;
        $this->requestOdbcInventoryService = $requestOdbcInventoryService;
    }

    public function inventoryMapper($inventoryNumber): void
    {
        $sql = $this->requestOdbcInventoryService->getUniqueInventoryLocations($inventoryNumber);
        $results = $this->odbcService->executeQuery($sql);

        foreach ($results as $result) {
            // Vérifie si l'INVENTORY_NUMBER existe déjà dans la base de données
            $existingLocation = $this->em->getRepository(Location::class)
                ->findOneBy(['inventoryNumber' => $result['INVENTORY_NUMBER']]);

            if (!$existingLocation && $result['LOCATION'] != null) {
                // Si l'INVENTORY_NUMBER n'existe pas, on crée une nouvelle location
                $location = new Location();
                $location->setWarehouse($result['WAREHOUSE']);
                $location->setLocation($result['LOCATION']);
                $location->setReferent($result['REFERENT']);
                $location->setStatus(0);
                $location->setInventoryNumber($result['INVENTORY_NUMBER']);

                $this->em->persist($location);
            }
        }
        $this->em->flush();
    }



    public function inventoryArticleMapper($inventoryNumber): void
    {
        $sql = $this->requestOdbcInventoryService->getArticlesWithLocation($inventoryNumber);
        $results = $this->odbcService->executeQuery($sql);

        foreach ($results as $result) {
            // Vérifie si l'INVENTORY_NUMBER existe déjà dans la base de données
            $existingArticle = $this->em->getRepository(InventoryArticle::class)
                ->findOneBy(['inventoryNumber' => $result['INVENTORY_NUMBER']]);

            if (!$existingArticle && $result['LOCATION'] != null) {
                // Si l'INVENTORY_NUMBER n'existe pas, on crée un nouvel article
                $inventoryArticle = new InventoryArticle();
                $inventoryArticle->setInventoryNumber($result['INVENTORY_NUMBER']);
                $inventoryArticle->setWarehouse($result['WAREHOUSE']);
                $inventoryArticle->setLocation($result['LOCATION']);
                $inventoryArticle->setArticleCode($result['CODE_ARTICLE']);
                $inventoryArticle->setDesignation1($result['DESIGNATION1']);
                $inventoryArticle->setDesignation2($result['DESIGNATION2']);
                $inventoryArticle->setLotCode($result['CODE_LOT']);
                $inventoryArticle->setDimensionType($result['TYPE_DIMENSION']);
                $inventoryArticle->setPackaging($result['CONDITIONNEMENT']);
                $inventoryArticle->setPackagingName($result['LIBELLE_CONDI']);
                $inventoryArticle->setQuantityLocation1($result['QUANTITE_LOC1']);
                $inventoryArticle->setPreparationUnit($result['UNITE_PREPARATION']);
                $inventoryArticle->setQuantity2Location1($result['QUANTITE2_LOC1']);
                $inventoryArticle->setTypeArticle($result['TYPE_ARTICLE']);
                $inventoryArticle->setDivisible($result['DIVISIBLE']);
                $inventoryArticle->setServedFromStock($result['SERVED_FROM_STOCK']);
                $inventoryArticle->setTheoricalQuantity($result['QUANTITE_THEORIQUE']);

                $this->em->persist($inventoryArticle);
            }

            if (!$existingArticle && $result['LOCATION2'] != null) {
                // Si l'INVENTORY_NUMBER n'existe pas, on crée un nouvel article
                $inventoryArticle = new InventoryArticle();
                $inventoryArticle->setInventoryNumber($result['INVENTORY_NUMBER']);
                $inventoryArticle->setWarehouse($result['WAREHOUSE']);
                $inventoryArticle->setLocation($result['LOCATION2']);
                $inventoryArticle->setArticleCode($result['CODE_ARTICLE']);
                $inventoryArticle->setDesignation1($result['DESIGNATION1']);
                $inventoryArticle->setDesignation2($result['DESIGNATION2']);
                $inventoryArticle->setLotCode($result['CODE_LOT']);
                $inventoryArticle->setDimensionType($result['TYPE_DIMENSION']);
                $inventoryArticle->setPackaging($result['CONDITIONNEMENT']);
                $inventoryArticle->setPackagingName($result['LIBELLE_CONDI']);
                $inventoryArticle->setQuantityLocation1($result['QUANTITE_LOC1']);
                $inventoryArticle->setPreparationUnit($result['UNITE_PREPARATION']);
                $inventoryArticle->setQuantity2Location1($result['QUANTITE2_LOC1']);
                $inventoryArticle->setTypeArticle($result['TYPE_ARTICLE']);
                $inventoryArticle->setDivisible($result['DIVISIBLE']);
                $inventoryArticle->setServedFromStock($result['SERVED_FROM_STOCK']);
                $inventoryArticle->setTheoricalQuantity($result['QUANTITE_THEORIQUE']);


                $this->em->persist($inventoryArticle);
            }

            if (!$existingArticle && $result['LOCATION3'] != null) {
                // Si l'INVENTORY_NUMBER n'existe pas, on crée un nouvel article
                $inventoryArticle = new InventoryArticle();
                $inventoryArticle->setInventoryNumber($result['INVENTORY_NUMBER']);
                $inventoryArticle->setWarehouse($result['WAREHOUSE']);
                $inventoryArticle->setLocation($result['LOCATION3']);
                $inventoryArticle->setArticleCode($result['CODE_ARTICLE']);
                $inventoryArticle->setDesignation1($result['DESIGNATION1']);
                $inventoryArticle->setDesignation2($result['DESIGNATION2']);
                $inventoryArticle->setLotCode($result['CODE_LOT']);
                $inventoryArticle->setDimensionType($result['TYPE_DIMENSION']);
                $inventoryArticle->setPackaging($result['CONDITIONNEMENT']);
                $inventoryArticle->setPackagingName($result['LIBELLE_CONDI']);
                $inventoryArticle->setQuantityLocation1($result['QUANTITE_LOC1']);
                $inventoryArticle->setPreparationUnit($result['UNITE_PREPARATION']);
                $inventoryArticle->setQuantity2Location1($result['QUANTITE2_LOC1']);
                $inventoryArticle->setTypeArticle($result['TYPE_ARTICLE']);
                $inventoryArticle->setDivisible($result['DIVISIBLE']);
                $inventoryArticle->setServedFromStock($result['SERVED_FROM_STOCK']);
                $inventoryArticle->setTheoricalQuantity($result['QUANTITE_THEORIQUE']);


                $this->em->persist($inventoryArticle);
            }
        }

        $this->em->flush();
    }
}
