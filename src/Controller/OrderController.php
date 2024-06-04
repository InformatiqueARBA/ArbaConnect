<?php

namespace App\Controller;

use App\Service\CsvGeneratorService;
use App\Service\OdbcService;
use App\Entity\Order;
use App\Enum\Status;
use App\Form\OrderType;
use App\Repository\CorporationRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\DatabaseSwitcherService;
use App\Service\DataMapperService;
use App\Service\RequestOdbcService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class OrderController extends AbstractController
{

    #[Route('/dates-livraisons', name: 'app_dates_livraisons')]
    // public function datesLivraisons(EntityManagerInterface $em, OrderRepository $orderRepository): Response
    public function datesLivraisons(DatabaseSwitcherService $databaseSwitcherService): Response
    {
        // $orders = $orderRepository->findAll();

        $boolDB = true;
        $doctrine = $databaseSwitcherService->getEntityManager($boolDB);
        // dd($doctrine);


        $orders = $doctrine->getRepository(Order::class)->findAll();
        // dd($orders);

        // $orders = $em->getRepository(Order::class)->findAll();

        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }






    #[Route('/detail/{id}/edit', name: 'app_edit')]
    public function edit(Order $order, Request $request, EntityManagerInterface $em, CsvGeneratorService $csvG): Response
    {

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // changement du statut de la commande en EDITED TODO: pour le moment le boutton
            // change de couleur et devient vert de façon permanante (voir si pertinant )
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






    #[Route('/mapperCorporation', name: 'mapperCorporation')]
    public function corporationMapper(DataMapperService $dataMapperService): JsonResponse
    {
        $sql = $dataMapperService->corporationMapper();
        return $this->json($sql);
    }

    #[Route('/mapperOrder', name: 'mapperOrder')]
    public function orderMapper(DataMapperService $dataMapperService): JsonResponse
    {
        $sql = $dataMapperService->orderMapper();
        return $this->json($sql);
    }

    #[Route('/testDelete', name: 'delete')]
    public function delete(DataMapperService $dataMapperService, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        // Get the connection from the entity manager
        $connection = $entityManager->getConnection();

        try {
            // Start the transaction
            $connection->beginTransaction();

            // Truncate the user table
            $connection->executeStatement('DELETE FROM User');

            // Truncate the order table
            $connection->executeStatement('DELETE FROM `order`');

            // Truncate the corporation table
            $connection->executeStatement('DELETE FROM Corporation');

            // Commit the transaction if all statements are successful
            $connection->commit();
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            $connection->rollBack();
            throw $e;
        }

        $dataMapperService->corporationMapper();
        $dataMapperService->orderMapper();
        $dataMapperService->userMapper();

        return new Response('yo');
    }
}
