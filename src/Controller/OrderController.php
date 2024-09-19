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
use App\Service\DataMapperService;
use App\Service\PopulateAcdbService;
use App\Service\RequestOdbcService;
use App\Service\SendARService;
use App\Service\TourCodeService;
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

        return $this->render('order/liste_commandes.html.twig', [
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


        return $this->render('order/liste_commandes.html.twig', [
            'orders' => $orders,
            'DB' => $databaseName,
        ]);
    }








    // TODO: interdire la soumission du form si la dte n'a pas été changée voir doc:https://symfony.com/doc/current/form/events.html
    #[Route('/commandes/detail/{id}/edit', name: 'app_edit')]
    public function edit(Request $request, CsvGeneratorService $csvG, String $id, DatabaseSwitcherService $databaseSwitcherService): Response
    {
        $em = $databaseSwitcherService->getEntityManager();
        $order = $em->getRepository(Order::class)->find($id);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);




        // si le formulaire est soumis, qu'il n'est pas valide et que l'erreur est "La date de livraison n'a pas été modifiée." envoi un flash à l'utilsateur
        if ($form->isSubmitted() && !$form->isValid() &&  $form->getErrors()[0]->getMessage() == "Erreur_DDL") {
            $this->addFlash('warning', "Veuillez renseigner la nouvelle date de livraison");
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // changement du statut de la commande en EDITED TODO: pour le moment le bouton
            // change de couleur et devient vert de façon permanente (voir si pertinant )
            // dd($order);

            $order->setOrderStatus(Status::EDITED);

            //------------------------------------------

            $nobon = $order->getId();

            $date = $order->getDeliveryDate();
            $formattedDate = $date->format('d/m/Y');


            $user = $this->getUser();



            if (!$user instanceof User) {
                throw new \LogicException('The user is not valid.');
            }



            //persistance en DB
            $em->persist($order);
            $em->flush();

            // création d'un message flash pour avertir de la modification
            $this->addFlash('success', 'la date de livraison à bien été modifiée');

            // Appel au service CsvGeneratorService pour générer le fichier csv RUBIS
            // Pendant la phase de test autorise uniquement ces Users ou l'env DEV. 
            //TODO: Vérifier pertinence FICTIF & travailler sur la casse login

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
    public function delete(
        PopulateAcdbService $populateAcdbService,
        DataMapperService $dataMapperService,
        DatabaseSwitcherService $databaseSwitcherService,
        ParameterBagInterface $params,
        OdbcService $odbcService,
        RequestOdbcService $requestOdbcService
    ): Response {
        $populateAcdbService->populateAcdb($dataMapperService, $databaseSwitcherService, $params, $odbcService, $requestOdbcService);

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
