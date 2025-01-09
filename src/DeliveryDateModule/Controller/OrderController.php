<?php

namespace App\DeliveryDateModule\Controller;

use App\DeliveryDateModule\Enum\Status;
use App\ArbaConnect\Service\DataMapperSecurityService;
use App\ArbaConnect\Service\OdbcService;
use App\DeliveryDateModule\Service\CsvGeneratorService;
use App\DeliveryDateModule\Service\DatabaseSwitcherService;
use App\DeliveryDateModule\Service\DataMapperService;
use App\DeliveryDateModule\Service\PopulateAcdbService;
use App\DeliveryDateModule\Service\RequestOdbcDeliveryDateService;
use App\DeliveryDateModule\Service\TourCodeService;
use App\Entity\Acdb\Order;
use App\Entity\Security\User;
use App\DeliveryDateModule\Form\OrderType;
use App\Entity\Acdb\OrderDetail;
use App\Entity\Security\ArbaTour;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;


class OrderController extends AbstractController
{

    private $em;
    private $params;

    public function __construct(DatabaseSwitcherService $databaseSwitcherService, ParameterBagInterface $params)
    {
        $this->em = $databaseSwitcherService->getEntityManager();
        $this->params = $params;
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

        return $this->render('DeliveryDateModule/order/liste_commandes.html.twig', [
            'orders' => $orders,
            'DB' => $databaseName,
        ]);
    }


    #[Route('/commandes/dates-livraisons-adherent', name: 'app_dates_livraisons_adherent')]
    public function datesLivraisonsAdherent(DatabaseSwitcherService $databaseSwitcherService): Response
    {
        $em = $databaseSwitcherService->getEntityManager();
        $user = $this->getUser();

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


        return $this->render('DeliveryDateModule/order/liste_commandes.html.twig', [
            'orders' => $orders,
            'DB' => $databaseName,
        ]);
    }








    // TODO: interdire la soumission du form si la dte n'a pas été changée voir doc:https://symfony.com/doc/current/form/events.html
    #[Route('/commandes/detail/{id}/edit', name: 'app_edit')]
    public function edit(Request $request, CsvGeneratorService $csvG, String $id, DatabaseSwitcherService $databaseSwitcherService, ManagerRegistry $managerRegistry): Response
    {
        $em = $databaseSwitcherService->getEntityManager();
        $order = $em->getRepository(Order::class)->find($id);

        $emSecurity = $managerRegistry->getManager('security');


        $user = $this->getUser();

        // Check if user is an instance of User class
        if (!$user instanceof User) {
            throw new \LogicException('The user is not valid.');
        }

        // prends en priorité le code postal de l'adresse de livraison et le code postal de l'adh si pas d'adresse de livraison
        if ($order->getZipCode() != null || $order->getZipCode() != '') {
            $zipCode = $emSecurity->getRepository(ArbaTour::class)->findTourCodeByZipCode($order->getZipCode()) ?: $user->getTourCode();
        } elseif ($order->getZipCode() == null || $order->getZipCode() == '') {
            $zipCode = $user->getTourCode();
        } else {
            $zipCode = '';
        }




        $orderDetails = $em->getRepository(OrderDetail::class)->findByOrderId($id);
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);


        // si le formulaire est soumis, qu'il n'est pas valide et que l'erreur est "La date de livraison n'a pas été modifiée." envoi un flash à l'utilsateur
        if ($form->isSubmitted() && !$form->isValid() &&  $form->getErrors()[0]->getMessage() == "Err_Saisie") {
            $this->addFlash('warning', "Veuillez renseigner la nouvelle date de livraison");
        }
        if ($form->isSubmitted() && !$form->isValid() &&  $form->getErrors()[0]->getMessage() == "Err_90jours") {
            $this->addFlash('warning', "La date de livraison ne peut excéder 90 jours par rapport à sa date de création.");
        }

        if ($form->isSubmitted() && $form->isValid()) {


            $date = $order->getDeliveryDate();
            $isValid = true;
            $flashDate = '';

            // Si la commande est ORA : test de livraison à J+10 mini
            if ($order->getType() == 'Sur ordre') {
                $minDateORA = (new DateTime())->modify('+10 days')->format('d/m/Y');

                // Délai non respecté, information transmise à l'adh
                if ($order->getDeliveryDate()->format('d/m/Y') < $minDateORA) {
                    $this->addFlash('warning', "Un délai de 10 jours est demandé pour les commandes sur ordre. La livraison est possible à partir du : " . $minDateORA);
                    return $this->redirectToRoute('app_edit', ['id' => $id]);
                    // Indique en base/CSV que le bon n'est plus un ORA
                } else {
                    $order->setType('ORC');
                }
            }

            // Boucle pour récupérer toutes les dates de réception fournisseur le cas échéant.
            foreach ($orderDetails as $orderDetail) {

                $receptionDateOriginal = $orderDetail->getReceptionDate();
                $receptionDatePlus7 = '';

                // Ajoute 7 jours à la date fournisseur la plus grande et teste le délai Arba (Order by géré dans le repo)
                if ($receptionDateOriginal != null) {
                    $receptionDatePlus7 = new \DateTime($receptionDateOriginal->format('d-m-Y'));
                    $receptionDatePlus7->modify('+7 days');
                }

                if ($receptionDatePlus7 != '' && $receptionDatePlus7 > $date) {
                    $isValid = false;

                    $flashDate = $orderDetail->getReceptionDate()->format('d-m-Y');
                    break;
                }
            }

            // Délai non respecté, information transmise à l'adh
            if ($isValid == false) {
                $this->addFlash('warning', "Un délai de 7 jours est demandé à partir de la date de réception de votre commande soit: " . $flashDate . " + 7 jours");
                return $this->redirectToRoute('app_edit', ['id' => $id]);
            }


            $order->setOrderStatus(Status::EDITED);
            $em->persist($order);
            $em->flush();


            // création d'un message flash pour avertir de la modification
            $this->addFlash('success', 'la date de livraison à bien été modifiée');

            // Appel au service CsvGeneratorService pour générer le fichier csv RUBIS
            // Pendant la phase de test autorise uniquement ces Users ou l'env DEV. 
            //TODO: Vérifier pertinence FICTIF & travailler sur la casse login


            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new \LogicException('The user is not valid.');
            }

            $allowedLogins = ['016253', '016FICTIF', '016god'];
            $login = $user->getLogin();

            //dd($this->getParameter(('kernel.environment')));
            if (in_array($login, $allowedLogins) || $this->getParameter('kernel.environment') === 'dev') {
                $csvG->deliveryDateCsv($order);
            }

            if (in_array('ROLE_ADMIN', $user->getRoles())) {

                return $this->redirectToRoute('app_dates_livraisons');
            } else {

                return $this->redirectToRoute('app_dates_livraisons_adherent');
            }
        }

        return $this->render('DeliveryDateModule/order/detail_commande.html.twig', [
            'form' => $form,
            'order' => $order,
            'zipCode' => $zipCode
        ]);
    }









    #[Route('/admin/odbc', name: 'odbc_index')]
    public function odbc(OdbcService $odbcService, RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService): JsonResponse
    {
        $sql = $requestOdbcDeliveryDateService->getCoporations();
        try {
            $results = $odbcService->executeQuery($sql);
            return $this->json($results);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }






    #[Route('/admin/testDelete', name: 'delete')]
    public function delete(
        PopulateAcdbService $populateAcdbService,
        DataMapperService $dataMapperService,
        DatabaseSwitcherService $databaseSwitcherService,
        ParameterBagInterface $params,
        OdbcService $odbcService,
        RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService
    ): Response {
        $populateAcdbService->populateAcdb($dataMapperService, $databaseSwitcherService, $params, $odbcService, $requestOdbcDeliveryDateService);

        return $this->redirectToRoute('app_dates_livraisons');
    }






    #[Route('/admin/userUpdate', name: 'userUpdate')]
    public function deuxdex(DataMapperSecurityService $dataMapperSecurityService): Response
    {
        $dataMapperSecurityService->userMapper();

        return new Response('Users are up to date.');
    }



    #[Route('/admin/orderDetail', name: 'orderDetail')]
    public function orderDetail(DataMapperService $dataMapperService, DatabaseSwitcherService $databaseSwitcherService, OdbcService $odbcService, RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService): Response
    {
        $dataMapperService->orderDetailMapper($databaseSwitcherService, $odbcService, $requestOdbcDeliveryDateService,);

        return new Response('orderDetails are up to date.');
    }



    #[Route('/admin/code', name: 'code')]
    public function code(TourCodeService $tourCodeService): Response
    {
        $tourCodeService->getCodeTour();

        return new Response('code ok!');
    }


    // #[Route('/arba/aradh', name: 'code')]
    // public function ARADH(SendARService $sendARService): Response
    // {
    //     $tab = [
    //         952732, 952734, 952806, 952807, 952808, 952832, 953122, 953154, 953525,
    //         953560, 953600, 953744, 953824, 953825, 953912, 954096, 954097, 954157, 954412, 954466,
    //         955558, 956509, 956535, 956540, 956574, 956678, 956846, 956847, 956848, 957072, 957073,
    //         957075, 957084, 957168, 957207, 957223, 957231, 957808, 957816, 957897, 957937, 958035,
    //         958158, 958250, 958329, 958473, 958496, 958535, 958536, 958551, 958656, 958776, 958864,
    //         958936, 959168, 959220, 959221, 959416, 959483, 959505, 959507, 959565, 959654, 959655,
    //         959656, 959673, 959675, 959695, 959724, 959743, 959843, 959847, 959991, 960107, 960130,
    //         960212, 960291, 960482, 960483, 960805
    //     ];
    //     foreach ($tab as $value) {
    //         $to = 'mcpa@9business.fr';
    //         $sendARService->sendAR2($value, $to); // Assurez-vous que sendAR2 accepte ces arguments.
    //     }

    //     return new Response('code ok!');
    // }

}
