<?php

namespace App\InventoryModule\Controller;


use App\Entity\Security\InventoryArticle;
use App\Entity\Security\Location;
use App\Entity\Security\User;
use App\InventoryModule\Form\InventoryArticlesCollectionType;

use App\InventoryModule\Service\CoutingPageXLSXService;
use App\InventoryModule\Service\DataMapperInventoryService;
use App\InventoryModule\Service\InventoryCSVRubisService;
use App\InventoryModule\Service\PrinterService;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends AbstractController
{






    // Liste des emplacements
    #[Route('/arba/inventaire/liste', name: 'app_inventory')]
    public function index(ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager('security');
        $locations = $em->getRepository(Location::class)->findAll();

        return $this->render('InventoryModule\liste_inventaire.html.twig', [
            'locations' => $locations,
        ]);
    }

    #[Route('/arba/inventaire/locations', name: 'app_inventory_locations')]
    public function getLocations(ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager('security');
        $locations = $em->getRepository(Location::class)->findAll();

        return $this->render('InventoryModule\partials\_locations.html.twig', [
            'locations' => $locations,
        ]);
    }








    #[Route('/arba/inventaire/detail/{warehouse}/{location}/edit', name: 'app_inventory_detail_edit')]
    public function inventoryDetailEdit(String $warehouse, String $location, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $location = urldecode($location);
        $em = $managerRegistry->getManager('security');

        // Récupérer le user connecté pour affecter le référent de la saisie
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

        // Trier les articles par ordre alphabétique de location
        usort($articleParLoc, function ($a, $b) {
            return strcmp(
                $a->getLocation(),
                $b->getLocation()
            );
        });

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

        return $this->render('InventoryModule/detail_inventaire.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
            'warehouse' => $warehouse,
        ]);
    }













    // Création du CSV inventaire Rubis
    #[Route('/admin/generationInventaire/{inventoryNumber?}', name: 'app_csvInventory')]
    public function generateCsvInventory(ManagerRegistry $managerRegistry, InventoryCSVRubisService $inventoryCSVSRubisService, $inventoryNumber = null): Response
    {
        $em = $managerRegistry->getManager('security');

        if ($inventoryNumber !== null) {

            $warehouse = $em->getRepository(Location::class)->findWarehouseByInventoryNumber($inventoryNumber);

            $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByInventoryNumberAndWarehouse($inventoryNumber, $warehouse);
            $inventoryCSVSRubisService->inventoryCsvArray($inventoryArticleByLoca, $inventoryNumber);
        }

        // Récupérer la liste des fichiers dans le répertoire
        $directory = '/var/www/ArbaConnect/public/csv/inventory/inventory_sheets/';
        $files = array_diff(scandir($directory), array('.', '..'));

        return $this->render('InventoryModule/inventory_setting_CSV_Rubis.twig', [
            'files' => $files,
        ]);
    }







    // Création des feuilles de comptage
    #[Route('/admin/inventaire/parametrage/edition/feuille-comptage/{data?}', name: 'app_inventory_setting_counting_page_edition')]
    public function inventoryCountingPageEdition(CoutingPageXLSXService $coutingPageXLSXService, ManagerRegistry $managerRegistry, PrinterService $printerService, $data = null): Response
    {
        $inventoryNumber = null;
        $printerName = null;
        if ($data != null) {
            $data = json_decode($data);
            $inventoryNumber = $data[0];
            $printerName = $data[1];
            set_time_limit(300);
        }



        $em = $managerRegistry->getManager('security');


        // Vérifier si $number est fourni
        if ($inventoryNumber !== null) {
            $warehouse = $em->getRepository(Location::class)->findWarehouseByInventoryNumber($inventoryNumber);
            $Locations = $em->getRepository(Location::class)->findByInventoryNumber($inventoryNumber);
            // dd($Locations);

            foreach ($Locations as $Location) {

                if (null != $Location->getLocation() && trim($Location->getLocation()) != '') {
                    // récupère tous les articles liés aux localisations d'un dépôt
                    $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByLocationAndWarehouse($warehouse, $Location->getLocation());
                    //dd($inventoryArticleByLoca);
                    // génère le fichier excel pour un localisation donnée


                    // $filePath = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/" . str_replace('/', '_', $Location->getLocation()) . ".pdf";
                    $filePath = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/" . str_replace(['/', ' '], ['_', ''], $Location->getWarehouse() . '_' . $Location->getLocation()) . ".pdf";


                    $coutingPageXLSXService->generateCountingXLSX($inventoryArticleByLoca, $Location->getLocation(), $filePath, $inventoryNumber);
                    //$coutingPageXLSXService->saveSpreadsheet($pdfWriter, $filePath);
                }
            }
        }

        if ($printerName != null) {
            $printerService->PDFPrinter($printerName);
        }
        /*       // Si ARBA1 & ARBA2
        if ($printerName != null && $printerName = 'ARBA1_2') {
            //$printerService->PDFPrinter('Accueil');
            $printerService->PDFPrinter('Menuiserie');
        } else { // Si 1 seule imprimante
            $printerService->PDFPrinter($printerName);
        }*/

        // Récupérer la liste des fichiers dans le répertoire
        $directory = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/';
        $files = array_diff(scandir($directory), array('.', '..'));

        $filesOnly = array_filter($files, function ($file) use ($directory) {
            return is_file($directory . $file);
        });

        $directoryPrinted = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/printed/';
        $filesPrinted = array_diff(scandir($directoryPrinted), array('.', '..'));

        return $this->render('InventoryModule/inventory_setting_counting_page_edition.html.twig', [
            'files' => $filesOnly,
            'filesPrinted' => $filesPrinted,
        ]);
    }








    // Mise en BDD des Articles de l'inventaire
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








    // Mise en BDD des Localisations de l'inventaire
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




    /// Routes vers page delete
    #[Route(
        '/admin/deleteInventory',
        name: 'app_inventory_delete_db'
    )]
    public function inventoryTruncateDB(): Response
    {
        return $this->render('InventoryModule/inventory_delete_db.html.twig', []);
    }


    // Supprimer une Localisation en base
    #[Route(
        '/admin/inventoryDeleteLocationByInventoryNumber/{inventoryNumberDBLocations?}',
        name: 'app_inventory_delete_location_by_inventory_number'
    )]
    public function deleteLocationByInventoryNumber(ManagerRegistry $managerRegistry, $inventoryNumberDBLocations = null): Response
    {
        if (null != $inventoryNumberDBLocations) {
            $em = $managerRegistry->getManager('security');
            $locations = $em->getRepository(Location::class)->findByInventoryNumber($inventoryNumberDBLocations);
            foreach ($locations as $location) {
                $em->persist($location);
                $em->remove($location);
            }
            $em->flush();
            $this->addFlash('success', "Les localisations de l'inventaire $inventoryNumberDBLocations ont été supprimées");
        }

        return $this->redirectToRoute('admin_inventory');
    }

    // Supprimer toutes les localisations
    #[Route(
        '/admin/inventoryTruncateDBLocation',
        name: 'app_inventory_truncate_all_locations_db'
    )]
    public function truncateDBLocation(ManagerRegistry $managerRegistry): Response
    {
        $conn = $managerRegistry->getConnection('security');
        $sql = 'TRUNCATE TABLE Location';
        $stmt = $conn->prepare($sql);
        $stmt->executeStatement();
        $this->addFlash('success', "La table 'Location' a été vidée.");
        return $this->redirectToRoute('admin_inventory');
    }






    /// Routes DELETE DB INVENTORY ARTICLES

    // Supprimer les articles liés à une Localisation 
    #[Route(
        '/admin/inventoryDeleteArticlesByInventoryNumber/{inventoryNumberDBArticles?}',
        name: 'app_inventory_delete_articles_by_inventory_number'
    )]
    public function deleteArticlesByInventoryNumber(ManagerRegistry $managerRegistry, string $inventoryNumberDBArticles): Response
    {

        if (null != $inventoryNumberDBArticles) {
            $em = $managerRegistry->getManager('security');
            $articles = $em->getRepository(InventoryArticle::class)->findByInventoryNumber($inventoryNumberDBArticles);
            foreach ($articles as $article) {
                $em->persist($article);
                $em->remove($article);
            }
            $em->flush();
            $this->addFlash('success', "Les articles de l'inventaire $inventoryNumberDBArticles ont été supprimées");
        }


        return $this->redirectToRoute('admin_inventory');
    }

    //Supprimer tous les articles
    #[Route(
        '/admin/inventoryTruncateDBInventoryArticles',
        name: 'app_inventory_truncate_all_articles_db'

    )]
    public function truncateDBInventoryAticles(ManagerRegistry $managerRegistry): Response
    {

        $conn = $managerRegistry->getConnection('security');
        $sql = 'TRUNCATE TABLE InventoryArticle';
        $stmt = $conn->prepare($sql);
        $stmt->executeStatement();
        $this->addFlash('success', "La table 'InventoryArticle' a été vidée.");
        return $this->redirectToRoute('admin_inventory');
    }


    //------------------------------------------------------------------------------------------------------------------------------


    // Populer les DB en dur via URL


    #[Route(
        '/admin/localisation',
        name: 'localisation'
    )]
    public function location(DataMapperInventoryService $dataMapperInventoryService): Response
    {
        $dataMapperInventoryService->inventoryMapper('002612');
        return new Response('Locations are up to date');
    }



    #[Route(
        '/admin/articleInventaire',
        name: 'articleInventaire'
    )]
    public function articleInventaire(DataMapperInventoryService $dataMapperInventoryService): Response
    {
        $dataMapperInventoryService->inventoryArticleMapper('002612');
        return new Response('articles are up to date');
    }





    // Impression du dossier PDF 
    #[Route(
        '/admin/impressionInventaire',
        name: 'impressionInventaire'
    )]
    public function Printer(PrinterService $printerService): Response
    {
        // $printerService->PDFPrinter();
        return $this->redirectToRoute('app_inventory_setting_counting_page_edition');
    }



    // test impression SAM 
    #[Route(
        '/admin/printerSAM',
        name: 'printerSAM'
    )]
    public function printerSam(PrinterService $printerService): Response
    {

        $printerName = 'Accueil';
        if ($printerName != null) {
            $printerService->printTestPDF($printerName);
        }
        return new Response('imprim Sam');
    }
}
