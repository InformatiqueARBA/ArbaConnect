<?php

namespace App\InventoryModule\Controller;


use App\Entity\Security\InventoryArticle;
use App\Entity\Security\Location;
use App\Entity\Security\User;
use App\InventoryModule\Form\InventoryArticlesCollectionType;
use App\InventoryModule\Form\InventoryArticlesCollectionTypeBlank;
use App\InventoryModule\Service\CoutingPageXLSXService;
use App\InventoryModule\Service\DataMapperInventoryService;
use App\InventoryModule\Service\InventoryCSVRubisService;
use App\InventoryModule\Service\PrinterService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



class InventoryController extends AbstractController
{
    // Menu selection inventaire (inventaire/lot)
    #[Route('/arba/inventaire/selection', name: 'app_selection')]
    public function select(): Response
    {
        return $this->render('InventoryModule\select.html.twig', []);
    }




    // Liste des emplacements stock
    #[Route('/arba/inventaire/liste', name: 'app_inventory')]
    public function index(ManagerRegistry $managerRegistry): Response
    {

        $em = $managerRegistry->getManager('security');
        $locations = $em->getRepository(Location::class)->findLocationsWithArtArticles();

        return $this->render('InventoryModule\liste_inventaire.html.twig', [
            'locations' => $locations,
        ]);
    }




    // Liste des emplacements lots
    #[Route('/arba/inventaire/liste/lot', name: 'app_inventory_lot')]
    public function indexLot(ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager('security');
        $locations = $em->getRepository(Location::class)->findLocationsWithLovArticles();

        return $this->render('InventoryModule\liste_inventaire_lot.html.twig', [
            'locations' => $locations,
        ]);
    }




    // Liste des articles présent dans une allée stock pour saisie des quantités
    #[Route('/arba/inventaire/detail/{warehouse}/{location}/{inventoryNumber}/edit', name: 'app_inventory_detail_edit')]
    public function inventoryDetailEdit(String $warehouse, String $location, String $inventoryNumber, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager('security');
        $Location = $em->getRepository(Location::class)->findByLocation($location);

        // permet la récupération des / présent dans le nom des allées via url
        $location = str_replace('®', '/', $location);

        // Récupérer le user connecté pour affecter le référent de la saisie
        $user = $this->getUser();

        // vérifie si le user est de type User class
        if (!$user instanceof User) {
            throw new \LogicException('The user is not valid.');
        }


        // Récupérer les articles
        $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationAndWarehouseAndArtType($inventoryNumber, $warehouse, $location);

        // Créer un tableau d'articles pour le formulaire
        $formData = ['articles' => $articleParLoc];

        // Stocker les valeurs originales des articles
        $originalArticlesData = [];
        foreach ($articleParLoc as $article) {
            $originalArticlesData[$article->getId()] = [
                'quantityLocation1' => $article->getQuantityLocation1()
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
                    $article->getQuantityLocation1() !== $originalData['quantityLocation1']
                ) {
                    $shouldSetReferent = true;
                }

                // Persister l'article dans tous les cas
                $em->persist($article);
            }

            $em->flush();

            $this->addFlash('success', 'Tous les articles ont été mis à jour avec succès.');


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
            'inventoryNumber' => $inventoryNumber,

        ]);
    }




    // Liste des articles présent dans une allée lot pour saisie des quantités
    #[Route('/arba/inventaire/detail/lot/{warehouse}/{location}/{inventoryNumber}/edit', name: 'app_inventory_detail_lot_edit')]
    public function inventoryDetailLotEdit(String $warehouse, String $location, String $inventoryNumber, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $location = urldecode($location);

        $em = $managerRegistry->getManager('security');
        $Location = $em->getRepository(Location::class)->findByLocation($location);

        // Récupérer le user connecté pour affecter le référent de la saisie
        $user = $this->getUser();

        // Check if user is an instance of User class
        if (!$user instanceof User) {
            throw new \LogicException('The user is not valid.');
        }



        $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationAndWarehouseAndLovType($inventoryNumber, $warehouse, $location);

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
                    $article->getQuantityLocation1() !== $originalData['quantityLocation1']
                ) {
                    $shouldSetReferent = true;
                }

                // Persister l'article dans tous les cas
                $em->persist($article);
            }

            $em->flush();

            $this->addFlash('success', 'Tous les articles ont été mis à jour avec succès.');

            if ($shouldSetReferent) {
                $Location[0]->setReferent($user->getLogin());
            }
            $em->persist($Location[0]);
            $em->flush();

            return $this->redirectToRoute('app_inventory_lot');
        }


        return $this->render('InventoryModule/detail_inventaire_lot.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
            'warehouse' => $warehouse,
            'inventoryNumber' => $inventoryNumber,

        ]);
    }




    // Page de création des articles non inventoriéss
    #[Route('/arba/inventaire/blank-page/{inventoryNumber}/{location}/{warehouse}/{typeArticle}', name: 'app_inventory_blank_page')]
    public function blankPage(String $warehouse, String $location, String $inventoryNumber, String $typeArticle, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager('security');


        $articleParLoc = [];
        // créér un Aticle non référencé 
        for (
            $i = 0;
            $i < 1;
            $i++
        ) {
            $article = new InventoryArticle();
            $article->setInventoryNumber($inventoryNumber);
            $article->setWarehouse($warehouse);
            $article->setLocation($location);
            $article->setArticleCode('');
            $article->setDesignation1('');
            $article->setDesignation2('');
            $article->setLotCode('');
            $article->setPreparationUnit('');
            if ($typeArticle == 'stock') {
                $article->setTypeArticle('ART');
            } elseif ($typeArticle == 'lot') {
                $article->setTypeArticle('LOV');
            } else {
                $article->setTypeArticle('');
            }

            $article->setDivisible('');
            $article->setUnknownArticle(true);

            $articleParLoc[] = $article;
        }


        $formData = ['articles' => $articleParLoc];

        $form = $this->createForm(InventoryArticlesCollectionTypeBlank::class, $formData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingArticles  = $em->getRepository(InventoryArticle::class)->findall();

            // vérifie si un article ayant le même code est présent en DB et si oui affecte les valeurs à l'article non référencé
            foreach ($formData['articles'] as $article) {
                foreach ($existingArticles as $one) {
                    if ($article->getArticleCode() === $one->getArticleCode()) {
                        $article->setDivisible($one->isDivisible());
                        $article->setDesignation1($one->getDesignation1());
                        $article->setDesignation2($one->getDesignation2());
                        $article->setPackagingName($one->getPackagingName());
                        $article->setPreparationUnit($one->getPreparationUnit());
                        $article->setTypeArticle($one->getTypeArticle());

                        break;
                    }
                }
                $em->persist($article);
            }

            $em->flush();
            $this->addFlash('success', 'Tous les articles ont été mis à jour avec succès.');

            // redirection vers l'allée ou a été créé l'article
            if ($typeArticle === 'lot') {
                return $this->redirectToRoute('app_inventory_detail_lot_edit', [
                    'warehouse' => $warehouse,
                    'location' => $location,
                    'inventoryNumber' => $inventoryNumber
                ]);
            } elseif ($typeArticle === 'stock') {
                return $this->redirectToRoute('app_inventory_detail_edit', [
                    'warehouse' => $warehouse,
                    'location' => $location,
                    'inventoryNumber' => $inventoryNumber
                ]);
            }
        }


        return $this->render('InventoryModule/blank_page.html.twig', [
            'form' => $form->createView(),

        ]);
    }




    // ADMIN  | Routes vers page selection delete DB
    #[Route('/admin/deleteInventory', name: 'app_inventory_delete_db')]
    public function inventoryTruncateDB(): Response
    {
        return $this->render('InventoryModule/inventory_delete_db.html.twig', []);
    }





    // ADMIN  | Création du CSV inventaire Stockés Rubis
    #[Route('/admin/generationInventaire/{inventoryNumber?}', name: 'app_csvInventory')]
    public function generateCsvInventory(ManagerRegistry $managerRegistry, InventoryCSVRubisService $inventoryCSVSRubisService, $inventoryNumber = null): Response
    {
        $em = $managerRegistry->getManager('security');

        if ($inventoryNumber !== null) {

            $warehouse = $em->getRepository(Location::class)->findWarehouseByInventoryNumber($inventoryNumber);

            $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByInventoryNumberAndWarehouseAndArtType($inventoryNumber, $warehouse);
            $inventoryCSVSRubisService->inventoryCsvArray($inventoryArticleByLoca, $inventoryNumber);
        }

        // Récupérer la liste des fichiers dans le répertoire stock
        $directory = '/var/www/ArbaConnect/public/csv/inventory/inventory_sheets/stock/';
        $files = array_diff(scandir($directory), array('.', '..'));

        return $this->render('InventoryModule/inventory_setting_CSV_Rubis.twig', [
            'files' => $files,
        ]);
    }




    // ADMIN  | Création du CSV inventaire Lot Rubis
    #[Route('/admin/generationInventaireLot/{inventoryNumber?}', name: 'app_csvInventoryLot')]
    public function generateCsvInventoryLot(ManagerRegistry $managerRegistry, InventoryCSVRubisService $inventoryCSVSRubisService, $inventoryNumber = null): Response
    {
        $em = $managerRegistry->getManager('security');

        if ($inventoryNumber !== null) {

            $warehouse = $em->getRepository(Location::class)->findWarehouseByInventoryNumber($inventoryNumber);

            $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByInventoryNumberAndWarehouseAndLovType($inventoryNumber, $warehouse);
            $inventoryCSVSRubisService->inventoryLotCsvArray($inventoryArticleByLoca, $inventoryNumber);
        }

        // Récupérer la liste des fichiers dans le répertoire lot
        $directory = '/var/www/ArbaConnect/public/csv/inventory/inventory_sheets/lot/';
        $files = array_diff(scandir($directory), array('.', '..'));

        return $this->render('InventoryModule/inventory_setting_CSV_LOT_Rubis.twig', [
            'files' => $files,
        ]);
    }




    // ADMIN  | Création des feuilles de comptage articles stockés
    #[Route('/admin/inventaire/parametrage/edition/feuille-comptage-stock/{data?}', name: 'app_inventory_setting_counting_page_edition_stock')]
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
            $Locations = $em->getRepository(Location::class)->findLocationsWithArtArticlesByinventoryNumber($inventoryNumber);

            $cptMax = 0;
            foreach ($Locations as $Location) {
                if (null != $Location->getLocation() && trim($Location->getLocation()) != '') {
                    // Récupérer tous les articles liés à cette localisation
                    $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)
                        ->findByLocationAndWarehouseAndArtType($inventoryNumber, $warehouse, $Location->getLocation());

                    $filePath = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/stock/"
                        . str_replace(['/', ' '], ['_', ''], $Location->getWarehouse() . '_' . $Location->getLocation()) . ".pdf";

                    $cptMax = $coutingPageXLSXService->countingMax($inventoryArticleByLoca, $Location->getLocation(), $filePath, $inventoryNumber);
                }
            }


            foreach ($Locations as $Location) {
                if (null != $Location->getLocation() && trim($Location->getLocation()) != '') {
                    // Récupérer tous les articles liés à cette localisation
                    $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)
                        ->findByLocationAndWarehouseAndArtType($inventoryNumber, $warehouse, $Location->getLocation());

                    $filePath = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/stock/"
                        . str_replace(['/', ' '], ['_', ''], $Location->getWarehouse() . '_' . $Location->getLocation()) . ".pdf";

                    $coutingPageXLSXService->generateCountingXLSXStock($inventoryArticleByLoca, $Location->getLocation(), $filePath, $inventoryNumber, $cptMax);
                }
            }
        }

        $directory = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/stock/';
        $destinationDirectory = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/printed/stock/";
        if ($printerName != null) {
            $printerService->PDFPrinter($printerName, $directory, $destinationDirectory);
        }

        // Récupérer la liste des fichiers dans le répertoire
        $files = array_diff(scandir($directory), array('.', '..'));
        $filesOnly = array_filter($files, function ($file) use ($directory) {
            return is_file($directory . $file);
        });

        $directoryPrinted = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/printed/stock/';
        $filesPrinted = array_diff(scandir($directoryPrinted), array('.', '..'));

        return $this->render('InventoryModule/inventory_setting_counting_page_edition.html.twig', [
            'files' => $filesOnly,
            'filesPrinted' => $filesPrinted,
        ]);
    }





    // ADMIN  | Création des feuilles de comptage lot
    #[Route('/admin/inventaire/parametrage/edition/feuille-comptage-lot/{data?}', name: 'app_inventory_setting_counting_page_edition_lot')]
    public function inventoryCountingPageLotEdition(CoutingPageXLSXService $coutingPageXLSXService, ManagerRegistry $managerRegistry, PrinterService $printerService, $data = null): Response
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
            $Locations = $em->getRepository(Location::class)->findLocationsWithLovArticlesByinventoryNumber($inventoryNumber);


            foreach ($Locations as $Location) {

                if (null != $Location->getLocation() && trim($Location->getLocation()) != '') {

                    // récupère tous les articles liés aux localisations d'un dépôt
                    $inventoryArticleByLoca = $em->getRepository(InventoryArticle::class)->findByLocationAndWarehouseAndLovType($inventoryNumber, $warehouse, $Location->getLocation());

                    $filePath = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/lot/" . str_replace(['/', ' '], ['_', ''], $Location->getWarehouse() . '_' . $Location->getLocation()) . ".pdf";


                    $coutingPageXLSXService->generateCountingXLSXLot($inventoryArticleByLoca, $Location->getLocation(), $filePath, $inventoryNumber);
                }
            }
        }

        $directory = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/lot/';
        $destinationDirectory = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/printed/lot/";
        if ($printerName != null) {

            $printerService->PDFPrinter($printerName,  $directory, $destinationDirectory);
        }

        // Récupérer la liste des fichiers dans le répertoire

        $files = array_diff(scandir($directory), array('.', '..'));

        $filesOnly = array_filter($files, function ($file) use ($directory) {
            return is_file($directory . $file);
        });

        $directoryPrinted = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/printed/lot/';
        $filesPrinted = array_diff(scandir($directoryPrinted), array('.', '..'));




        return $this->render('InventoryModule/inventory_setting_counting_page_edition_LOT.html.twig', [
            'files' => $filesOnly,
            'filesPrinted' => $filesPrinted,
        ]);
    }




    // Permet aux utilisateurs de consulter l'aide en ligne du module
    #[Route('/arba/inventaire/help', name: 'app_inventory_help')]
    public function inventoryHelp(): Response
    {
        return $this->render('InventoryModule/inventory_help.html.twig');
    }




    //  ADMIN  |  Mise en BDD des Articles de l'inventaire
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




    // ADMIN  |   Mise en BDD des Localisations de l'inventaire
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




    // ADMIN  |  liste des articles non référencés
    #[Route('/admin/liste-article-non-reference', name: 'app_inventory_list_unknown_article')]
    public function inventoryUnknownArticleList(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $em = $managerRegistry->getManager('security');
        $articlesUnknown = $em->getRepository(InventoryArticle::class)->findByUnknownArticleTag();


        $formData = ['articles' => $articlesUnknown];

        $form = $this->createForm(InventoryArticlesCollectionTypeBlank::class, $formData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($formData['articles'] as $articlesUnknown) {
                $em->persist($articlesUnknown);
            }
            $em->flush();
            $this->addFlash('success', 'Tous les articles ont été mis à jour avec succès.');

            return $this->redirectToRoute('app_inventory_list_unknown_article');
        }

        return $this->render(
            'InventoryModule/inventory_unknown_article_list.html.twig',
            [
                'articlesUnknown' => $articlesUnknown,
                'form' => $form
            ]
        );
    }




    // ADMIN  | route pour la suppression d'article non référencé
    #[Route('/admin/liste-article-non-reference/supprimer/{id}', name: 'app_inventory_delete_unknown_article', methods: ['GET'])]
    public function deleteUnknownArticle(ManagerRegistry $managerRegistry, int $id): Response
    {
        $em = $managerRegistry->getManager('security');

        // Récupération de l'entité à supprimer
        $article = $em->getRepository(InventoryArticle::class)->find($id);

        if (!$article) {
            $this->addFlash('error', 'Article introuvable.');
            return $this->redirectToRoute('app_inventory_list_unknown_article');
        }

        try {
            // Supprimer l'entité
            $em->remove($article);
            $em->flush();

            $this->addFlash(
                'success',
                'Article supprimé avec succès.'
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression: ' . $e->getMessage());
        }
        return $this->redirectToRoute('app_inventory_list_unknown_article');
    }




    // ADMIN  | Supprimer une Localisation en base
    #[Route('/admin/inventoryDeleteLocationByInventoryNumber/{inventoryNumberDBLocations?}', name: 'app_inventory_delete_location_by_inventory_number')]
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




    // ADMIN  |  Supprimer toutes les localisations
    #[Route('/admin/inventoryTruncateDBLocation',  name: 'app_inventory_truncate_all_locations_db')]
    public function truncateDBLocation(ManagerRegistry $managerRegistry): Response
    {
        $conn = $managerRegistry->getConnection('security');
        $sql = 'TRUNCATE TABLE Location';
        $stmt = $conn->prepare($sql);
        $stmt->executeStatement();
        $this->addFlash('success', "La table 'Location' a été vidée.");
        return $this->redirectToRoute('admin_inventory');
    }




    // ADMIN  | Supprimer les articles liés à une Localisation
    #[Route('/admin/inventoryDeleteArticlesByInventoryNumber/{inventoryNumberDBArticles?}', name: 'app_inventory_delete_articles_by_inventory_number')]
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




    // ADMIN  | Supprimer tous les articles
    #[Route('/admin/inventoryTruncateDBInventoryArticles', name: 'app_inventory_truncate_all_articles_db')]
    public function truncateDBInventoryAticles(ManagerRegistry $managerRegistry): Response
    {

        $conn = $managerRegistry->getConnection('security');
        $sql = 'TRUNCATE TABLE InventoryArticle';
        $stmt = $conn->prepare($sql);
        $stmt->executeStatement();
        $this->addFlash('success', "La table 'InventoryArticle' a été vidée.");
        return $this->redirectToRoute('admin_inventory');
    }
}
