<?php

namespace App\InventoryModule\Controller;

use App\ArbaConnect\Service\OdbcService;
use App\InventoryModule\Service\DataMapperInventoryService;
use App\InventoryModule\Service\RequestOdbcInventoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends AbstractController
{
    #[Route('/inventaire', name: 'app_inventory')]
    public function index(): Response
    {

        return $this->render('InventoryModule\liste_inventaire.html.twig', [
            'controller_name' => 'InventoryController',
        ]);
    }

    #[Route('/inventaire/test', name: 'inventest')]
    public function inventaire(RequestOdbcInventoryService $requestOdbcInventoryService, OdbcService $odbcService): Response
    {
        $sql = $requestOdbcInventoryService->getInventory('002612');
        //$sql = $requestOdbcInventoryService->getMembers();
        $results = $odbcService->executeQuery($sql);

        dd($results);
        //dd($dataMapperInventoryService);
        return $this->redirectToRoute('app_inventory');
    }




    #[Route('/admin/localisation', name: 'localisation')]
    public function location(DataMapperInventoryService $dataMapperInventoryService): Response
    {
        $dataMapperInventoryService->inventoryMapper('002612');
        return new Response('Locations are up to date');
    }
}
