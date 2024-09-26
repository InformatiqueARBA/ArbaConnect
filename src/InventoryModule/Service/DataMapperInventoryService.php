<?php

namespace App\InventoryModule\Service;

use App\ArbaConnect\Service\OdbcService;
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



    //
    public function inventoryMapper($inventoryNumber): void
    {
        $sql = $this->requestOdbcInventoryService->getInventory($inventoryNumber);
        $results = $this->odbcService->executeQuery($sql);

        foreach ($results as $result) {
            // Vérifie si l'INVENTORY_NUMBER existe déjà dans la base de données
            $existingLocation = $this->em->getRepository(Location::class)
                ->findOneBy(['inventoryNumber' => $result['INVENTORY_NUMBER']]);

            if (!$existingLocation) {
                // Si l'INVENTORY_NUMBER n'existe pas, on crée une nouvelle location
                $location = new Location();
                $location->setWarehouse($result['WAREHOUSE']);
                $location->setLocation($result['LOCATION']);
                $location->setReferent($result['REFERENT']);
                $location->setStatus(0);
                $location->setInventoryNumber($result['INVENTORY_NUMBER']);

                $this->em->persist($location);
            } else {
                // Vous pouvez ajouter une action si l'inventaire existe déjà, si nécessaire
            }
        }

        $this->em->flush();
    }
}
