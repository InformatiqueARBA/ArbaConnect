<?php

namespace App\Service;

use App\Service\DatabaseSwitcherService;
use App\Service\DataMapperService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class PopulateAcdbService
{

    private $dataMapperService;
    private $databaseSwitcherService;
    private $variableDataSwitcher;


    public function __construct(DataMapperService $dataMapperService, DatabaseSwitcherService $databaseSwitcherService, ParameterBagInterface $params)
    {
        $this->dataMapperService = $dataMapperService;
        $this->databaseSwitcherService = $databaseSwitcherService;
        $this->variableDataSwitcher = $params->get('variables_app_directory');
    }

    public function populateAcdb(): Void
    {

        // Construire le chemin complet vers le fichier variableDataSwitcher.txt
        $filePath = $this->variableDataSwitcher . DIRECTORY_SEPARATOR . 'variableDataSwitcher.txt';

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            throw new \Exception("Le fichier $filePath n'existe pas.");
        }


        // Get the connection from the entity manager
        $connection = $this->databaseSwitcherService->getEntityManagerPopulate()->getConnection();
        // dd($connection);

        // try {
            // Start the transaction
            $connection->beginTransaction();

            // // Truncate the member table
            $connection->executeStatement('DELETE FROM Member');

            // // Truncate the order table
            $connection->executeStatement('DELETE FROM `order`');

            // Truncate the corporation table
            $connection->executeStatement('DELETE FROM Corporation');

            // Commit the transaction if all statements are successful
            $connection->commit();
        // } catch (\Exception $e) {
        //     // Rollback the transaction in case of error
        //     $connection->rollBack();
        //     throw $e;
        // }
        $connection->close();
        

        $this->dataMapperService->corporationMapper(($this->databaseSwitcherService));
        $this->dataMapperService->orderMapper(($this->databaseSwitcherService));
        $this->dataMapperService->MemberMapper(($this->databaseSwitcherService));

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
