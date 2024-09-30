<?php

namespace App\InventoryModule\Controller;

use App\ArbaConnect\Service\OdbcService;
use App\Entity\Security\InventoryArticle;
use App\Entity\Security\Location;
use App\InventoryModule\Form\InventoryArticlesCollectionType;
use App\InventoryModule\Form\InventoryArticleType;
use App\InventoryModule\Service\DataMapperInventoryService;
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




    // #[Route('/arba/inventaire/detail/{location}/edit', name: 'app_edit2')]
    // public function edit2(String $location, ManagerRegistry $managerRegistry): Response
    // {
    //     $location = urldecode($location);
    //     $em = $managerRegistry->getManager('security');



    //     // changement du statut de l'objet Location
    //     $statusLocation = $em->getRepository(Location::class)->findByLocation($location);
    //     $statusLocation[0]->setStatus(1);
    //     $em->persist($statusLocation[0]);
    //     $em->flush();


    //     $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3($location);


    //     // créer un tableau de formulaires pour chaque article
    //     $forms = [];
    //     foreach ($articleParLoc as $article) {
    //         $forms[] = $this->createForm(InventoryArticleType::class, $article)->createView();
    //     }
    //     // dd($forms);
    //     return $this->render('InventoryModule\detail_inventaireSam.html.twig', [
    //         'articles' => $articleParLoc,
    //         'location' => $location,
    //         'forms' => $forms, // commenter si 2
    //     ]);
    // }

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



    // #[Route('/arba/inventaire/detail/{location}/edit', name: 'app_edit2')]
    // public function edit2(Request $request, String $location, ManagerRegistry $managerRegistry): Response
    // {
    //     // Décodage de l'emplacement
    //     $location = urldecode($location);

    //     // Récupération du gestionnaire d'entité
    //     $em = $managerRegistry->getManager('security');

    //     // Récupérer l'objet Location et mettre à jour son statut
    //     $statusLocation = $em->getRepository(Location::class)->findByLocation($location);
    //     if ($statusLocation) {
    //         $statusLocation[0]->setStatus(1);
    //         $em->persist($statusLocation[0]);
    //         $em->flush();
    //     }

    //     // Récupérer les articles pour cet emplacement (location, location2 ou location3)
    //     $articleParLoc = $em->getRepository(InventoryArticle::class)->findByLocationOrLocation2OrLocation3($location);

    //     // Tableau pour stocker les articles avec leurs formulaires
    //     $articlesWithForms = [];
    //     $isFormSubmitted = false;

    //     // Parcours de chaque article lié à cet emplacement
    //     foreach ($articleParLoc as $index => $article) {
    //         // Création d'un formulaire pour chaque article
    //         $form = $this->createForm(InventoryArticleType::class, $article);
    //         //dd($form->handleRequest($request));
    //         $form->handleRequest($request);

    //         // Vérification si le formulaire a été soumis et est valide
    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $isFormSubmitted = true; // Marqueur indiquant qu'au moins un formulaire est soumis
    //             // Persister les modifications de l'article
    //             $em->persist($article);
    //         }

    //         // Ajout de l'article et de son formulaire au tableau pour affichage dans la vue
    //         $articlesWithForms[] = [
    //             'article' => $article,
    //             'form' => $form->createView(),
    //         ];
    //     }

    //     // Si au moins un formulaire a été soumis et validé
    //     if ($isFormSubmitted) {
    //         // Sauvegarde des modifications dans la base de données
    //         $em->flush();

    //         // Ajouter un message flash de succès
    //         $this->addFlash('success', 'Inventaire mis à jour avec succès.');

    //         // Redirection pour éviter une soumission multiple du formulaire (PRG pattern)
    //         return $this->redirectToRoute('app_edit2', ['location' => urlencode($location)]);
    //     }

    //     // Rendu de la vue Twig avec les articles et leurs formulaires
    //     return $this->render('InventoryModule/detail_inventaire4.html.twig', [
    //         'articlesWithForms' => $articlesWithForms,
    //         'location' => $location,
    //     ]);
    // }







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
