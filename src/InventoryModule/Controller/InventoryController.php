<?php

namespace App\InventoryModule\Controller;

use App\ArbaConnect\Service\OdbcService;
use App\Entity\Security\InventoryArticle;
use App\Entity\Security\Location;
use App\InventoryModule\Form\InventoryArticlesCollectionType;
use App\InventoryModule\Form\InventoryArticleType;
use App\InventoryModule\Service\CoutingPageXLSXService;
use App\InventoryModule\Service\DataMapperInventoryService;
use App\InventoryModule\Service\InventoryCSVRubisService;
use App\InventoryModule\Service\RequestOdbcInventoryService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function edit2(String $location, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $location = urldecode($location);
        $em = $managerRegistry->getManager('security');

        // Changement du statut de l'objet Location à actif
        $statusLocation = $em->getRepository(Location::class)->findByLocation($location);
        $statusLocation[0]->setStatus(1);
        $em->persist($statusLocation[0]);
        $em->flush();

        // Récupérer les articles
        $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3($location);

        // Créer un tableau d'articles pour le formulaire
        $formData = ['articles' => $articleParLoc];

        // Créer le formulaire parent avec la collection d'articles
        $form = $this->createForm(InventoryArticlesCollectionType::class, $formData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traiter chaque article et enregistrer
            foreach ($formData['articles'] as $article) {
                $em->persist($article);
            }
            $em->flush();

            $this->addFlash('success', 'Tous les articles ont été mis à jour avec succès.');
            // return $this->redirectToRoute('app_edit2', ['location' => $location]);

            // Changement du statut de l'objet Location à inactif
            $statusLocation[0]->setStatus(0);
            $em->persist($statusLocation[0]);
            $em->flush();


            return $this->redirectToRoute('app_inventory');
        }

        return $this->render('InventoryModule/detail_inventaireSam.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
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



    #[Route('/admin/xlsx', name: 'xlsx')]
    public function xlsx(CoutingPageXLSXService $coutingPageXLSXService): Response
    {
        $data = [
            ['nom' => 'Dupont', 'prenom' => 'Jean'],
            ['nom' => 'Martin', 'prenom' => 'Sophie'],
            ['nom' => 'Durand', 'prenom' => 'Paul'],
            ['nom' => 'Petit', 'prenom' => 'Emma'],
            ['nom' => 'Lemoine', 'prenom' => 'Louis'],
            ['nom' => 'Moreau', 'prenom' => 'Lucie'],
            ['nom' => 'Fournier', 'prenom' => 'Hugo'],
            ['nom' => 'Roux', 'prenom' => 'Alice'],
            ['nom' => 'Blanc', 'prenom' => 'Thomas'],
            ['nom' => 'Garnier', 'prenom' => 'Chloé'],
            ['nom' => 'Faure', 'prenom' => 'Matthieu'],
            ['nom' => 'Chevalier', 'prenom' => 'Julie'],
            ['nom' => 'Renard', 'prenom' => 'Pierre'],
            ['nom' => 'Schmitt', 'prenom' => 'Marion'],
            ['nom' => 'Leroux', 'prenom' => 'Antoine'],
        ];

        $filePath = '/var/www/ArbaConnect/public/csv/inventory/test.xlsx';
        $spreadsheet = $coutingPageXLSXService->generateXlsx($data);
        $coutingPageXLSXService->saveSpreadsheet($spreadsheet, $filePath);

        return new Response('test XLSX');
    }

    #[Route('/admin/xlsx2', name: 'xlsx2')]
    public function xlsx2(CoutingPageXLSXService $coutingPageXLSXService, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager('security');
        $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3('A1 01');


        $filePath = '/var/www/ArbaConnect/public/csv/inventory/test2.xlsx';
        $spreadsheet = $coutingPageXLSXService->generateCountingXLSX($inventoryArticleByLoca);
        $coutingPageXLSXService->saveSpreadsheet($spreadsheet, $filePath);

        return new Response('test XLSX2');
    }



    //TODO: C'est pas fini mais ça fonctionne (un peu ^^)
    // L'accès se fait via la page : http://ac.test/admin/inventaires
    #[Route('/admin/generationInventaire/{inventoryNumber}', name: 'app_csvInventory')]
    public function generateCsvInventory(string $inventoryNumber, ManagerRegistry $managerRegistry, InventoryCSVRubisService $inventoryCSVSRubisService, InventoryArticle $inventoryArticle): Response
    {
        $em = $managerRegistry->getManager('security');

        // $inventoryArticle = $em->getRepository(InventoryArticle::class)->findOneBy([
        //     'inventoryNumber' => $inventoryNumber,
        // ]);
        // // dd($inventoryArticle);

        // if (!$inventoryArticle) {
        //     throw $this->createNotFoundException('Article non trouvé pour l\'ID ' . $inventoryNumber);
        // }

        // $csvData = $inventoryCSVSRubisService->inventoryCsv($inventoryArticle);
        // dd($csvData);



        //test sur localisation  findByLocationOrLocation2OrLocation3
        $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3('A1 01');
        $inventoryCSVSRubisService->inventoryCsvArray($inventoryArticleByLoca);

        return new Response('CSV data generated and displayed.');
    }
}
