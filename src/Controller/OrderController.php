<?php

namespace App\Controller;

use App\Entity\Acdb\Order;
use App\Entity\Security\User;
use App\Service\CsvGeneratorService;
use App\Service\OdbcService;
use App\Enum\Status;
use App\Form\OrderType;
use App\Service\DatabaseSwitcherService;
use App\Service\DataMapperSecurityService;
use App\Service\PopulateAcdbService;
use App\Service\RequestOdbcService;
use App\Service\SendARService;
use App\Service\TourCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{

    private $em;

    public function __construct(DatabaseSwitcherService $databaseSwitcherService)
    {
        $this->em = $databaseSwitcherService->getEntityManager();
    }






    #[Route('/arba', name: 'app_dates_livraisons')]
    public function datesLivraisons(DatabaseSwitcherService $databaseSwitcherService): Response
    {
        $em = $databaseSwitcherService->getEntityManager();

        //pour afficher la DB-------------------------
        $connection = $em->getConnection();
        $databaseName = $connection->getDatabase();
        //fin pour afficher la DB-------------------------

        $orders = $em->getRepository(Order::class)->findAll();

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'DB' => $databaseName,
        ]);
    }


    #[Route('/commandes/dates-livraisons-adherent', name: 'app_dates_livraisons_adherent')]
    public function datesLivraisonsAdherent(DatabaseSwitcherService $databaseSwitcherService, Security $security): Response
    {
        $em = $databaseSwitcherService->getEntityManager();
        $user = $security->getUser();

        // Check if user is an instance of User class
        if (!$user instanceof User) {
            throw new \LogicException('The user is not valid.');
        }

        $enterprise = $user->getEnterprise();

        // Display the DB name
        $connection = $em->getConnection();
        $databaseName = $connection->getDatabase();

        // $orders = $em->getRepository(Order::class)->findBy(['corporationId' => $enterprise]);
        $orders = $em->getRepository(Order::class)->findByCorporationId($enterprise);


        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'DB' => $databaseName,
        ]);
    }







    #[Route('/commandes/detail/{id}/edit', name: 'app_edit')]
    public function edit(Request $request, CsvGeneratorService $csvG, String $id, DatabaseSwitcherService $databaseSwitcherService, SendARService $sendARService, Security $security): Response
    {
        $em = $databaseSwitcherService->getEntityManager();
        $order = $em->getRepository(Order::class)->find($id);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);


        //TODO: decommmenter les 2 lignes et assigné $mail_AR à la variable $to de la fonction $sendARService->sendAR($nobon, $formattedDate, $to); pour envoi à ADh
        $user = $security->getUser();
        $mail_AR = $user->getMailAR();



        if ($form->isSubmitted() && $form->isValid()) {
            // changement du statut de la commande en EDITED TODO: pour le moment le bouton
            // change de couleur et devient vert de façon permanente (voir si pertinant )
            // dd($order);

            $order->setOrderStatus(Status::EDITED);

            //------------------------------------------

            $nobon = $order->getId();

            $date = $order->getDeliveryDate();
            $formattedDate = $date->format('d/m/Y');
            $to = '';
            $sendARService->sendAR($nobon, $formattedDate, $to);


            //------------------------------------------



            //persistance en DB
            $em->persist($order);
            $em->flush();

            // création d'un message flash pour avertir de la modification
            $this->addFlash('success', 'la date de livraison à bien été modifiée');

            // Appel au service CsvGeneratorService pour généré le fichier csv RUBIS
            $csvG->deliveryDateCsv($order);

            return $this->redirectToRoute('app_dates_livraisons_adherent');
        }



        return $this->render('order/detail_commande.html.twig', [
            'form' => $form,
            'order' => $order
        ]);
    }









    #[Route('/commandes/odbc', name: 'odbc_index')]
    public function odbc(OdbcService $odbcService, RequestOdbcService $requestOdbcService): JsonResponse
    {
        $sql = $requestOdbcService->getCoporations();
        try {
            $results = $odbcService->executeQuery($sql);
            return $this->json($results);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }






    #[Route('/commandes/testDelete', name: 'delete')]
    public function delete(PopulateAcdbService $populateAcdbService): Response
    {
        $populateAcdbService->populateAcdb();

        return $this->redirectToRoute('app_dates_livraisons');
    }






    #[Route('/commandes/userUpdate', name: 'userUpdate')]
    public function deuxdex(DataMapperSecurityService $dataMapperSecurityService): Response
    {
        $dataMapperSecurityService->userMapper();

        return new Response('Users are up to date.');
    }

    #[Route('/arba/commandes/code', name: 'code')]
    public function code(TourCodeService $tourCodeService): Response
    {
        $tourCodeService->getCodeTour();

        return new Response('code ok!');
    }
}
