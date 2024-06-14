<?php

namespace App\Service;

use App\Entity\Acdb\Corporation;
use App\Entity\Acdb\Member;
use App\Entity\Acdb\Order;
use Doctrine\ORM\EntityManagerInterface;

//class permettant de Requêter Rubis et de persister les données dans la BDD applicative
//fait appel au service RequestOdbcService pour obtenir les requêtes
//fait appel au service OdbcService pour exécuter les requêtes
class DataMapperService
{


    private $requestOdbcService;
    private $odbcService;
    private $em;



    public function __construct(RequestOdbcService $requestOdbcService, OdbcService $odbcService, DatabaseSwitcherService $databaseSwitcherService)
    {
        $this->requestOdbcService = $requestOdbcService;
        $this->odbcService = $odbcService;
        $this->em = $databaseSwitcherService->getEntityManagerPopulate();
    }






    //fonction pour peupler la table corporation de la BDD ACDB
    public function corporationMapper(): void
    {
        $sql = $this->requestOdbcService->getCoporations();

        $results = $this->odbcService->executeQuery($sql);
        // dd($results);

        foreach ($results as $result) {

            $corporation = new Corporation();
            $corporation->setId($result['ID']);
            $corporation->setName($result['NAME']);
            $corporation->setStatus($result['STATUS']);

            $this->em->persist($corporation);
        }

        $this->em->flush();
    }






    //fonction pour peupler la table order de la BDD ACDB
    public function orderMapper(): void
    {
        $sql = $this->requestOdbcService->getOrders();
        $results = $this->odbcService->executeQuery($sql);

        foreach ($results as $result) {

            $corporation = $this->em->getRepository(Corporation::class)->findOneBy(['id' => $result['CORPORATIONID']]);

            $order = new Order();
            $order->setId($result['ID']);
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

            $this->em->persist($order);
        }

        $this->em->flush();
    }



    //fonction pour peupler la table order de la BDD ACDB
    public function MemberMapper(): void
    {
        $sql = $this->requestOdbcService->getMembers();
        $results = $this->odbcService->executeQuery($sql);

        foreach ($results as $result) {


            $corporation = $this->em->getRepository(Corporation::class)->findOneBy(['id' => $result['CORPORATIONID']]);

            $Member = new Member();
            $Member->setId($result['ID']);
            $Member->setCorporation($corporation);
            $Member->setProfil($result['PROFIL']);
            $Member->setPassword($result['PASSWORD']);
            $Member->setMail($result['MAIL']);
            $Member->setFirstName($result['FIRSTNAME']);
            $Member->setLastName($result['LASTNAME']);



            $this->em->persist($Member);
        }

        $this->em->flush();
    }
}
