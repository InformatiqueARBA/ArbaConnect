<?php

namespace App\DeliveryDateModule\Service;

use App\ArbaConnect\Service\OdbcService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class PopulateAcdbService
{


    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    // private $dataMapperService;
    // private $databaseSwitcherService;
    // private $variableDataSwitcher;


    // public function __construct(DataMapperService $dataMapperService, DatabaseSwitcherService $databaseSwitcherService, ParameterBagInterface $params)
    // {
    //     // $this->dataMapperService = $dataMapperService;
    //     // $this->databaseSwitcherService = $databaseSwitcherService;
    //     $this->variableDataSwitcher = $params->get('variables_app_directory');
    // }

    public function populateAcdb(
        DataMapperService $dataMapperService,
        DatabaseSwitcherService $databaseSwitcherService,
        ParameterBagInterface $params,
        OdbcService $odbcService,
        RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService
    ): void {
        // Construire le chemin complet vers le fichier variableDataSwitcher.txt
        $filePath = $params->get('variables_app_directory') . DIRECTORY_SEPARATOR . 'variableDataSwitcher.txt';

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            throw new \Exception("Le fichier $filePath n'existe pas.");
        }

        // Obtenir la connexion à partir de l'EntityManager
        $connection = $databaseSwitcherService->getEntityManagerPopulate()->getConnection();

        try {
            // Désactiver temporairement les contraintes de clés étrangères
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

            // Truncate des tables
            $connection->executeStatement('TRUNCATE TABLE Member');
            $connection->executeStatement('TRUNCATE TABLE OrderDetail');
            $connection->executeStatement('TRUNCATE TABLE `order`');
            $connection->executeStatement('TRUNCATE TABLE Corporation');

            // Réactiver les contraintes de clés étrangères
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');

            $this->logger->critical('************************TRUNCATE SUCCESSFUL **************************** ');
        } catch (\Exception $e) {
            // En cas d'erreur, réactiver les contraintes (par précaution)
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
            $this->logger->critical('************************TRUNCATE FAILED **************************** ');
            throw $e;
        } finally {
            $connection->close();
        }

        // Appel des méthodes de mapping
        $dataMapperService->corporationMapper($databaseSwitcherService, $odbcService, $requestOdbcDeliveryDateService);
        $dataMapperService->orderMapper($databaseSwitcherService, $odbcService, $requestOdbcDeliveryDateService);
        $dataMapperService->orderDetailMapper($databaseSwitcherService, $odbcService, $requestOdbcDeliveryDateService);
        $dataMapperService->MemberMapper($databaseSwitcherService, $odbcService, $requestOdbcDeliveryDateService);

        // Mise à jour du fichier variableDataSwitcher.txt
        $file = fopen($filePath, "r+");
        $dbByDefault = file_get_contents($filePath);

        fwrite($file, $dbByDefault == 1 ? 0 : 1);
        fclose($file);
    }
}
