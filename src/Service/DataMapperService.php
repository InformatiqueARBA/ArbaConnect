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


    // private $requestOdbcService;
    // // private $odbcService;
    // private $em;



    // public function __construct(RequestOdbcService $requestOdbcService, OdbcService $odbcService)
    // {
    //     $this->requestOdbcService = $requestOdbcService;
    //     // $this->odbcService = $odbcService;
    //     //$this->em = $databaseSwitcherService->getEntityManagerPopulate();
    // }






    //fonction pour peupler la table corporation de la BDD ACDB
    public function corporationMapper(DatabaseSwitcherService $databaseSwitcherService, OdbcService $odbcService, RequestOdbcService $requestOdbcService): void
    {
        $sql = $requestOdbcService->getCoporations();
        $results = $odbcService->executeQuery($sql);
        $em = $databaseSwitcherService->getEntityManagerPopulate();

        foreach ($results as $result) {
            // Vérifie si la corporation existe déjà dans la base de données


            // Sinon, on crée une nouvelle corporation
            $corporation = new Corporation();
            $corporation->setId($result['ID']);
            $corporation->setName($result['NAME']);
            $corporation->setStatus($result['STATUS']);

            $em->persist($corporation);
        }

        $em->flush();
        //$em->close();
    }







    //fonction pour peupler la table order de la BDD ACDB
    public function orderMapper(DatabaseSwitcherService $databaseSwitcherService, OdbcService $odbcService, RequestOdbcService $requestOdbcService): void
    {
        $sql = $requestOdbcService->getOrders();
        $results = $odbcService->executeQuery($sql);
        $em = $databaseSwitcherService->getEntityManagerPopulate();

        foreach ($results as $result) {

            $corporation = $em->getRepository(Corporation::class)->findOneBy(['id' => $result['CORPORATIONID']]);

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

            $em->persist($order);
        }

        $em->flush();
        //$em->close();
    }



    //fonction pour peupler la table order de la BDD ACDB
    public function MemberMapper(DatabaseSwitcherService $databaseSwitcherService, OdbcService $odbcService, RequestOdbcService $requestOdbcService): void
    {
        $sql = $requestOdbcService->getMembers();
        $results = $odbcService->executeQuery($sql);
        $em = $databaseSwitcherService->getEntityManagerPopulate();

        foreach ($results as $result) {


            $corporation = $em->getRepository(Corporation::class)->findOneBy(['id' => $result['CORPORATIONID']]);

            $Member = new Member();
            $Member->setId($result['ID']);
            $Member->setCorporation($corporation);
            $Member->setProfil($result['PROFIL']);
            $Member->setPassword($result['PASSWORD']);
            $Member->setMail($result['MAIL']);
            $Member->setFirstName($result['FIRSTNAME']);
            $Member->setLastName($result['LASTNAME']);



            $em->persist($Member);
        }

        $em->flush();
        $em->close();
    }
}
