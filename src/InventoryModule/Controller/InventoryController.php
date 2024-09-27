<?php

namespace App\InventoryModule\Controller;

use App\ArbaConnect\Service\OdbcService;
use App\Entity\Security\InventoryArticle;
use App\Entity\Security\Location;
use App\InventoryModule\Service\DataMapperInventoryService;
use App\InventoryModule\Service\RequestOdbcInventoryService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends AbstractController
{




    #[Route('/arba/inventaire/liste', name: 'app_inventory')]
    public function index(ManagerRegistry $managerRegistry): Response
    {

        $em = $managerRegistry->getManager('security');
        $locations = $em->getRepository(Location::class)->findAll();

        return $this->render('InventoryModule\liste_inventaire.html.twig', [
            'locations' => $locations,
        ]);
    }





    #[Route('/arba/inventaire/detail/localisation', name: 'app_detailLocalisation')]
    public function detailLocation(ManagerRegistry $managerRegistry): Response
    {

        $em = $managerRegistry->getManager('security');



        $articles = $em->getRepository(InventoryArticle::class)->findAll();

        return $this->render('InventoryModule\detail_inventaire.html.twig', [
            'articles' => $articles,
        ]);
    }




    #[Route('/arba/inventaire/detail/{location}/edit', name: 'app_edit2')]
    public function edit2(String $location, ManagerRegistry $managerRegistry): Response
    {
        $location = urldecode($location);
        $em = $managerRegistry->getManager('security');

        // changement du statut de l'objet Location
        $statusLocation = $em->getRepository(Location::class)->findByLocation($location);
        $statusLocation[0]->setStatus(1);
        $em->persist($statusLocation[0]);
        $em->flush();


        $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3($location);

        return $this->render('InventoryModule\detail_inventaire2.html.twig', [
            'articles' => $articleParLoc,
            'location' => $location
        ]);
    }


    #[Route('/admin/localisation', name: 'localisation')]
    public function location(DataMapperInventoryService $dataMapperInventoryService): Response
    {
        $dataMapperInventoryService->inventoryMapper('002612');
        return new Response('Locations are up to date');
    }






    #[Route('/admin/articleInventaire', name: 'articleInventaire')]
    public function articleInventaire(DataMapperInventoryService $dataMapperInventoryService): Response
    {
        $dataMapperInventoryService->inventoryArticleMapper('002612');
        return new Response('articles are up to date');
    }
}
