<?php

namespace App\Service;

use App\Entity\Corporation;
use App\Entity\Order;
use App\Repository\CorporationRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

//class permettant de Requêter Rubis et de persister les données dans la BDD applicative
//fait appel au service RequestOdbcService pour obtenir les requêtes
//fait appel au service OdbcService pour exécuter les requêtes
class DataMapperService
{


    private $requestOdbcService;
    private $odbcService;

    public function __construct(RequestOdbcService $requestOdbcService, OdbcService $odbcService)
    {
        $this->requestOdbcService = $requestOdbcService;
        $this->odbcService = $odbcService;
    }






    //fonction pour peupler la table corporation de la BDD ACDB
    public function corporationMapper(EntityManagerInterface $em, CorporationRepository $corporationRepository): void
    {
        $sql = $this->requestOdbcService->getCoporations();

        $results = $this->odbcService->executeQuery($sql);

        foreach ($results as $result) {

            $corporation = $corporationRepository->findOneBy(['id' => $result['ID']]);

            if (!$corporation) {
                $corporation = new Corporation();
                $corporation->setId($result['ID']);
            }

            $corporation->setName($result['NAME']);
            $corporation->setStatus($result['STATUS']);

            $em->persist($corporation);
        }

        $em->flush();
    }






    //fonction pour peupler la table order de la BDD ACDB
    public function orderMapper(EntityManagerInterface $em, OrderRepository $orderRepository, CorporationRepository $corporationRepository): void
    {
        $sql = $this->requestOdbcService->getOrders();
        $results = $this->odbcService->executeQuery($sql);

        foreach ($results as $result) {

            $order = $orderRepository->findOneBy(['id' => $result['ID']]);
            $corporation = $corporationRepository->findOneBy(['id' => $result['CORPORATIONID']]);

            if (!$order) {
                $order = new Order();
                $order->setId($result['ID']);
            }

            //formattage des dates pour mariaDB
            $orderDate = \DateTime::createFromFormat('Y-m-d', $result['ORDERDATE']);
            $deliveryDate = \DateTime::createFromFormat('Y-m-d', $result['DELIVERYDATE']);

            $order->setCorporation($corporation);
            $order->setOrderStatus($result['ORDERSTATUS']);
            $order->setReference($result['REFERENCE']);
            $order->setOrderDate($orderDate);
            $order->setDeliveryDate($deliveryDate);
            $order->setType($result['TYPE']);
            $order->setSeller($result['SELLER']);
            $order->setComment($result['COMMENT']);

            $em->persist($order);
        }

        $em->flush();
    }
}
