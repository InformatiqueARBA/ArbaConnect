<?php

namespace App\DeliveryDateModule\Service;

use App\ArbaConnect\Service\OdbcService;
use App\Entity\Acdb\Corporation;
use App\Entity\Acdb\Member;
use App\Entity\Acdb\Order;
use App\Entity\Acdb\OrderDetail;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

//class permettant de Requêter Rubis et de persister les données dans la BDD applicative
//fait appel au service RequestOdbcService pour obtenir les requêtes
//fait appel au service OdbcService pour exécuter les requêtes
class DataMapperService
{


    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }








    //fonction pour peupler la table corporation de la BDD ACDB
    public function corporationMapper(DatabaseSwitcherService $databaseSwitcherService, OdbcService $odbcService, RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService): void
    {
        $sql = $requestOdbcDeliveryDateService->getCoporations();
        $results = $odbcService->executeQuery($sql);
        $em = $databaseSwitcherService->getEntityManagerPopulate();

        foreach ($results as $result) {

            $corporation = new Corporation();
            $corporation->setId($result['ID']);
            $corporation->setName($result['NAME']);
            $corporation->setStatus($result['STATUS']);

            $em->persist($corporation);
        }

        $em->flush();
    }







    //fonction pour peupler la table order de la BDD ACDB
    public function orderMapper(DatabaseSwitcherService $databaseSwitcherService, OdbcService $odbcService, RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService): void
    {
        $sql = $requestOdbcDeliveryDateService->getOrders();
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
            $order->setZipCode($result['ZIPCODE']);



            $em->persist($order);
        }

        $em->flush();
        //$em->close();
    }

    public function orderDetailMapper(
        DatabaseSwitcherService $databaseSwitcherService,
        OdbcService $odbcService,
        RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService
    ): void {
        $sql = $requestOdbcDeliveryDateService->getDetailOrders();
        $results = $odbcService->executeQuery($sql);
        $em = $databaseSwitcherService->getEntityManagerPopulate();

        $batchSize = 50; // Taille du lot
        $counter = 0;

        foreach ($results as $result) {
            try {
                // Recherche de la commande associée
                $order = $em->getRepository(Order::class)->findOneBy(['id' => $result['NUM_BON']]);

                if (!$order) {
                    throw new \Exception("Commande introuvable pour NUM_BON : " . $result['NUM_BON']);
                }

                $orderDetail = new OrderDetail();
                $orderDetail->setId($result['NUM_BON'] . '_' . $result['NUM_LIG']);
                $orderDetail->setItemNumber($result['NUM_ART']);
                $orderDetail->setLabel($result['DESI']);
                $orderDetail->setQuantity((float)$result['QTE']);
                $orderDetail->setUnity($result['UNI']);
                $orderDetail->setOraQuantity((float)$result['QTE']);

                $orderDate = \DateTime::createFromFormat('Y-m-d', $result['DATE_COMMANDE']);
                $receptionDate = $result['DATE_RECEPTION']
                    ? \DateTime::createFromFormat('Y-m-d', $result['DATE_RECEPTION'])
                    : null;

                $orderDetail->setOrderDate($orderDate);
                $orderDetail->setReceptionDate($receptionDate);
                $orderDetail->setOraDeliveryDate(null);
                $orderDetail->setOrderNumber($result['NUM_BON']);
                $orderDetail->setLineNumber($result['NUM_LIG']);
                $orderDetail->setSupplierOrderNumber($result['NUM_BON_FOU']);
                $orderDetail->setSupplierConfirmation($result['CONF_FOU']);
                $orderDetail->setLineType($result['TYP_LIG']);
                $orderDetail->setCommand($order);


                $em->persist($orderDetail);

                // Incrementer le compteur
                $counter++;

                // Si le compteur atteint la taille du lot, flush et clear
                if ($counter % $batchSize === 0) {
                    $em->flush();
                    $em->clear(); // Nettoie l'EntityManager pour libérer la mémoire
                }
            } catch (\Exception $e) {
                // Log l'erreur avec le bon en cause
                // TODO: a décommenter quand corriger
                // $this->logger->critical('Erreur lors du traitement de NUM_BON ' . $result['NUM_BON'] . ': ' . $e->getMessage());
                // Continuer la boucle pour traiter les autres résultats
                continue;
            }
        }

        // Flush final pour les éléments restants
        $em->flush();
        $em->clear();
    }




    //fonction pour peupler la table order de la BDD ACDB
    public function MemberMapper(DatabaseSwitcherService $databaseSwitcherService, OdbcService $odbcService, RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService): void
    {
        $sql = $requestOdbcDeliveryDateService->getMembers();
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
    }
}
