<?php

namespace App\Controller;

use App\Service\CsvGenerator;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Enum\Status;
use App\Form\OrderType;
use App\Repository\CorporationRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/dates-livraisons', name: 'app_dates_livraisons')]
    public function datesLivraisons(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();

        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }


    #[Route('/detail/{id}/edit', name: 'app_edit')]
    public function edit(Order $order, Request $request, EntityManagerInterface $em, CsvGenerator $csvG): Response
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

            // Appel au service CsvGenerator pour généré le fichier csv RUBIS
            $csvG->deliveryDateCsv($order);

            return $this->redirectToRoute('app_dates_livraisons');
        }



        return $this->render('order/detail_commande.html.twig', [
            'form' => $form,
            'order' => $order
        ]);
    }
}
