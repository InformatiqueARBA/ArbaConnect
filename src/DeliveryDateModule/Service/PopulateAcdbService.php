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

    public function populateAcdb(DataMapperService $dataMapperService, DatabaseSwitcherService $databaseSwitcherService, ParameterBagInterface $params, OdbcService $odbcService, RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService): Void
    {


        // Construire le chemin complet vers le fichier variableDataSwitcher.txt
        $filePath = $params->get('variables_app_directory') . DIRECTORY_SEPARATOR . 'variableDataSwitcher.txt';

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            throw new \Exception("Le fichier $filePath n'existe pas.");
        }


        // Get the connection from the entity manager
        $connection = $databaseSwitcherService->getEntityManagerPopulate()->getConnection();
        // dd($connection);

        try {
            // Start the transaction
            $connection->beginTransaction();

            // // Truncate the member table
            $connection->executeStatement('DELETE FROM Member');

            // // Truncate the order table
            $connection->executeStatement('DELETE FROM `order`');

            // Truncate the corporation table
            $connection->executeStatement('DELETE FROM Corporation');
            $this->logger->critical('************************DELETE **************************** ');

            // Commit the transaction if all statements are successful
            $connection->commit();
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            $connection->rollBack();
            $this->logger->critical('************************ROLLBACK **************************** ');
            throw $e;
        }
        $connection->close();


        $dataMapperService->corporationMapper($databaseSwitcherService, $odbcService, $requestOdbcDeliveryDateService);
        $dataMapperService->orderMapper($databaseSwitcherService, $odbcService, $requestOdbcDeliveryDateService);
        $dataMapperService->MemberMapper($databaseSwitcherService, $odbcService, $requestOdbcDeliveryDateService);
        // r+ :Ouvre en lecture et écriture et place le pointeur de fichier au début du fichier.
        $file = fopen($filePath, "r+");
        $dbByDefault = file_get_contents($filePath);

        if ($dbByDefault == 1) {
            fwrite($file, 0);
        } else {
            fwrite($file, 1);
        }
        fclose($file);
    }
}
