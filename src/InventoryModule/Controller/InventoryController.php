<?php

namespace App\InventoryModule\Controller;

use App\ArbaConnect\Service\OdbcService;
use App\Entity\Security\InventoryArticle;
use App\Entity\Security\Location;
use App\Entity\Security\User;
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



    // #[Route('/arba/inventaire/detail/{location}/edit', name: 'app_inventory_detail_edit')]
    // public function inventoryDetailEdit(String $location, Request $request, ManagerRegistry $managerRegistry): Response
    // {
    //     $location = urldecode($location);
    //     $em = $managerRegistry->getManager('security');

    //     // on récupère le user connecté pour affecter le référent de la saisie
    //     $user = $this->getUser();

    //     // Check if user is an instance of User class
    //     if (!$user instanceof User) {
    //         throw new \LogicException('The user is not valid.');
    //     }

    //     // Changement du statut de l'objet Location à actif
    //     $Location = $em->getRepository(Location::class)->findByLocation($location);
    //     $Location[0]->setStatus(1);
    //     $em->persist($Location[0]);
    //     $em->flush();

    //     // Récupérer les articles
    //     $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3($location);

    //     // Créer un tableau d'articles pour le formulaire
    //     $formData = ['articles' => $articleParLoc];

    //     // Créer le formulaire parent avec la collection d'articles
    //     $form = $this->createForm(InventoryArticlesCollectionType::class, $formData);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Traiter chaque article et enregistrer
    //         foreach ($formData['articles'] as $article) {
    //             $em->persist($article);
    //         }
    //         $em->flush();

    //         $this->addFlash('success', 'Tous les articles ont été mis à jour avec succès.');
    //         // return $this->redirectToRoute('app_edit2', ['location' => $location]);

    //         // Changement du statut de l'objet Location à inactif
    //         $Location[0]->setStatus(0);
    //         $Location[0]->setReferent($user->getLogin());
    //         $em->persist($Location[0]);
    //         $em->flush();


    //         return $this->redirectToRoute('app_inventory');
    //     }

    //     return $this->render('InventoryModule/detail_inventaireSam.html.twig', [
    //         'form' => $form->createView(),
    //         'location' => $location,
    //     ]);
    // }

    // #[Route('/arba/inventaire/detail/{location}/edit', name: 'app_inventory_detail_edit')]
    // public function inventoryDetailEdit(String $location, Request $request, ManagerRegistry $managerRegistry): Response
    // {
    //     $location = urldecode($location);
    //     $em = $managerRegistry->getManager('security');

    //     // on récupère le user connecté pour affecter le référent de la saisie
    //     $user = $this->getUser();

    //     // Check if user is an instance of User class
    //     if (!$user instanceof User) {
    //         throw new \LogicException('The user is not valid.');
    //     }

    //     // Changement du statut de l'objet Location à actif
    //     $Location = $em->getRepository(Location::class)->findByLocation($location);
    //     $Location[0]->setStatus(1);
    //     $em->persist($Location[0]);
    //     $em->flush();

    //     // Récupérer les articles
    //     $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3($location);

    //     // Créer un tableau d'articles pour le formulaire
    //     $formData = ['articles' => $articleParLoc];

    //     // Créer le formulaire parent avec la collection d'articles
    //     $form = $this->createForm(InventoryArticlesCollectionType::class, $formData);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $shouldSetReferent = false; // Initialize flag

    //         // Traiter chaque article et enregistrer
    //         foreach ($formData['articles'] as $article) {
    //             // Check if either quantity field has a value
    //             if ($article->getQuantityLocation1() !== null || $article->getQuantity2Location1() !== null) {
    //                 $shouldSetReferent = true;
    //             }
    //             $em->persist($article);
    //         }
    //         $em->flush();

    //         $this->addFlash('success', 'Tous les articles ont été mis à jour avec succès.');

    //         // Changement du statut de l'objet Location à inactif
    //         $Location[0]->setStatus(0);

    //         // Only set the referent if at least one article has a quantity
    //         if ($shouldSetReferent) {
    //             $Location[0]->setReferent($user->getLogin());
    //         }

    //         $em->persist($Location[0]);
    //         $em->flush();

    //         return $this->redirectToRoute('app_inventory');
    //     }

    //     return $this->render('InventoryModule/detail_inventaireSam.html.twig', [
    //         'form' => $form->createView(),
    //         'location' => $location,
    //     ]);
    // }

    #[Route('/arba/inventaire/detail/{warehouse}/{location}/edit', name: 'app_inventory_detail_edit')]
    public function inventoryDetailEdit(String $warehouse, String $location, Request $request, ManagerRegistry $managerRegistry): Response
    {

        $location = urldecode($location);
        $em = $managerRegistry->getManager('security');

        // on récupère le user connecté pour affecter le référent de la saisie
        $user = $this->getUser();

        // Check if user is an instance of User class
        if (!$user instanceof User) {
            throw new \LogicException('The user is not valid.');
        }

        // Changement du statut de l'objet Location à actif
        $Location = $em->getRepository(Location::class)->findByLocation($location);
        $Location[0]->setStatus(1);
        $em->persist($Location[0]);
        $em->flush();

        // Récupérer les articles
        $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationAndWarehouse($warehouse, $location);

        // Créer un tableau d'articles pour le formulaire
        $formData = ['articles' => $articleParLoc];

        // Stocker les valeurs originales des articles
        $originalArticlesData = [];
        foreach ($articleParLoc as $article) {
            $originalArticlesData[$article->getId()] = [
                'quantityLocation1' => $article->getQuantityLocation1(),
                'quantity2Location1' => $article->getQuantity2Location1(),
            ];
        }

        // Créer le formulaire parent avec la collection d'articles
        $form = $this->createForm(InventoryArticlesCollectionType::class, $formData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shouldSetReferent = false; // Initialize flag

            // Traiter chaque article et vérifier les modifications
            foreach ($formData['articles'] as $article) {
                $originalData = $originalArticlesData[$article->getId()];

                // Vérifier si les quantités ont changé
                if (
                    $article->getQuantityLocation1() !== $originalData['quantityLocation1'] ||
                    $article->getQuantity2Location1() !== $originalData['quantity2Location1']
                ) {
                    $shouldSetReferent = true;
                }

                // Persister l'article dans tous les cas
                $em->persist($article);
            }

            $em->flush();

            $this->addFlash('success', 'Tous les articles ont été mis à jour avec succès.');

            // Changement du statut de l'objet Location à inactif
            $Location[0]->setStatus(0);

            // Only set the referent if at least one article has been modified
            if ($shouldSetReferent) {
                $Location[0]->setReferent($user->getLogin());
            }

            $em->persist($Location[0]);
            $em->flush();

            return $this->redirectToRoute('app_inventory');
        }

        return $this->render('InventoryModule/detail_inventaireSam2.html.twig', [

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





    // #[Route('/admin/xlsx', name: 'app_inventory_counting_xlsx')]
    // public function xlsx2(CoutingPageXLSXService $coutingPageXLSXService, ManagerRegistry $managerRegistry): Response
    // {
    //     $em = $managerRegistry->getManager('security');
    //     $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByLocationAndWarehouse('AQA', 'A1 01');


    //     $filePath = '/var/www/ArbaConnect/public/csv/inventory/test2.xlsx';
    //     $spreadsheet = $coutingPageXLSXService->generateCountingXLSX($inventoryArticleByLoca);
    //     $coutingPageXLSXService->saveSpreadsheet($spreadsheet, $filePath);

    //     return new Response('test XLSX');
    // }



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
        $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByLocationAndWarehouse('AQA', 'A1 01');
        $inventoryCSVSRubisService->inventoryCsvArray($inventoryArticleByLoca);

        return new Response('CSV data generated and displayed.');
    }






    #[Route('/admin/inventaire/parametrage/edition/feuille-comptage/{number?}', name: 'app_inventory_setting_counting_page_edition')]
    public function inventoryCountingPageEdition(CoutingPageXLSXService $coutingPageXLSXService, ManagerRegistry $managerRegistry, $number = null): Response
    {
        $em = $managerRegistry->getManager('security');

        // Vérifier si $number est fourni
        if ($number !== null) {
            $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3($number);

            // Chemin du fichier basé sur $number
            $filePath = "/var/www/ArbaConnect/public/csv/inventory/$number.xlsx";
            $spreadsheet = $coutingPageXLSXService->generateCountingXLSX($inventoryArticleByLoca);
            $coutingPageXLSXService->saveSpreadsheet($spreadsheet, $filePath);
        }

        // Récupérer la liste des fichiers dans le répertoire
        $directory = '/var/www/ArbaConnect/public/csv/inventory';
        $files = array_diff(scandir($directory), array('.', '..'));

        return $this->render('InventoryModule/inventory_setting_counting_page_edition.html.twig', [
            'files' => $files,
        ]);
    }






    #[Route('/admin/populer-db-articles-inventaire/{inventoryNumberDBArticles?}', name: 'app_inventory_populate_inventory_articles_db')]
    public function populateInventoryArticlesDB(DataMapperInventoryService $dataMapperInventoryService, $inventoryNumberDBArticles = null): Response
    {

        if ($inventoryNumberDBArticles !== null) {
            $dataMapperInventoryService->inventoryArticleMapper($inventoryNumberDBArticles);
            $this->addFlash('success', "Les articles de l'inventaire $inventoryNumberDBArticles ont été mis en base");
            return $this->redirectToRoute('admin_inventory');
        }

        return $this->render('InventoryModule/inventory_populate_inventory_articles_db.html.twig', []);
    }


    #[Route('/admin/populer-db-localisations-inventaire/{inventoryNumberDBLocations?}', name: 'app_inventory_populate_inventory_locations_db')]
    public function populateInventoryLocationsDB(DataMapperInventoryService $dataMapperInventoryService, $inventoryNumberDBLocations = null): Response
    {

        if ($inventoryNumberDBLocations !== null) {
            $dataMapperInventoryService->inventoryMapper($inventoryNumberDBLocations);
            $this->addFlash('success', "Les localisations de l'inventaire $inventoryNumberDBLocations ont été mises en base");
            return $this->redirectToRoute('admin_inventory');
        }

        return $this->render('InventoryModule/inventory_populate_inventory_locations_db.html.twig', []);
    }
}
