<?php

namespace App\DeliveryDateModule\Service;

use App\ArbaConnect\Service\OdbcService;
use Doctrine\ORM\EntityManagerInterface;

//class permettant de Requêter Rubis pour obtenir les codes tournée
//fait appel au service RequestOdbcService pour obtenir les requêtes
//fait appel au service OdbcService pour exécuter les requêtes
class TourCodeService
{
    private $requestOdbcDeliveryDateService;
    private $odbcService;

    public function __construct(RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService, OdbcService $odbcService)
    {
        $this->requestOdbcDeliveryDateService = $requestOdbcDeliveryDateService;
        $this->odbcService = $odbcService;
    }

    public function getCodeTour()
    {
        $sql = $this->requestOdbcDeliveryDateService->getTourCodes();
        $results = $this->odbcService->executeQuery($sql);

        $csvFilePath = '/var/www/ArbaConnect/public/csv/tour_code/tour_code.csv';
        $this->writeToCsv($csvFilePath, $results);
    }

    private function writeToCsv(string $filePath, array $data)
    {
        // Vérifier si le fichier existe
        // if (file_exists($filePath)) {
        //     // Si le fichier existe, le supprimer pour le recréer
        //     unlink($filePath);
        // }

        // Ouvrir le fichier en mode écriture
        $file = fopen($filePath, 'w');

        // Set the delimiter to ;
        fputcsv($file, array_keys($data[0]), ';'); // Assuming first row exists and has keys

        // Write data rows with ;
        foreach ($data as $row) {
            fputcsv($file, $row, ';');
        }

        fclose($file);
    }
}
