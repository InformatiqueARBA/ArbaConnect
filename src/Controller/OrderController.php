<?php

namespace App\Controller;


use App\Service\CsvGeneratorService;
use App\Service\OdbcService;
use App\Entity\Order;
use App\Enum\Status;
use App\Form\OrderType;
use App\Scheduler\Message\WriteInFileMessage;
use App\Service\DatabaseSwitcherService;
use App\Service\PopulateAcdbService;
use App\Service\RequestOdbcService;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;


class OrderController extends AbstractController
{

    private $em;

    public function __construct(DatabaseSwitcherService $databaseSwitcherService)
    {
        $this->em = $databaseSwitcherService->getEntityManager();
    }






    #[Route('/dates-livraisons', name: 'app_dates_livraisons')]
    // public function datesLivraisons(EntityManagerInterface $em, OrderRepository $orderRepository): Response
    public function datesLivraisons(DatabaseSwitcherService $databaseSwitcherService): Response
    {
        $em = $databaseSwitcherService->getEntityManager();
        //$em = $this->em;

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






    #[Route('/detail/{id}/edit', name: 'app_edit')]
    public function edit(Request $request, CsvGeneratorService $csvG, String $id, DatabaseSwitcherService $databaseSwitcherService): Response
    {
        $em = $databaseSwitcherService->getEntityManager();
        //$em = $this->em;
        $order = $em->getRepository(Order::class)->find($id);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // changement du statut de la commande en EDITED TODO: pour le moment le bouton
            // change de couleur et devient vert de façon permanente (voir si pertinant )

            $order->setOrderStatus(Status::EDITED);

            //persistance en DB
            $em->persist($order);
            $em->flush();

            // création d'un message flash pour avertir de la modification
            $this->addFlash('success', 'la date de livraison à bien été modifiée');

            // Appel au service CsvGeneratorService pour généré le fichier csv RUBIS
            $csvG->deliveryDateCsv($order);

            return $this->redirectToRoute('app_dates_livraisons');
        }



        return $this->render('order/detail_commande.html.twig', [
            'form' => $form,
            'order' => $order
        ]);
    }







    #[Route('/odbc', name: 'odbc_index')]
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






    #[Route('/testDelete', name: 'delete')]
    public function delete(PopulateAcdbService $populateAcdbService): Response
    {
        $populateAcdbService->populateAcdb();

        return $this->redirectToRoute('app_dates_livraisons');
    }
}
