<?php

namespace App\Controller;

use App\Service\CsvGenerator;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use App\Repository\CorporationRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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



    #[Route('/detail-commande/{id}/', name: 'app_detail_commandes')]
    public function show(OrderRepository $orderRepository, OrderType $orderType, Order $order, OrderDetailRepository $orderDetailRepository, CsvGenerator $csvG): Response
    {



        $oderdetail = $orderDetailRepository->findByCommandId($order->getId());
        // dd($oderdetail);
        $csvG->deliveryDateCsv($oderdetail);
        // 





        // $order = new Order();
        $form = $this->createForm(OrderType::class, $order);

        return $this->render('order/detail_commande.html.twig', [
            'form' => $form
        ]);
    }
}
