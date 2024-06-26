<?php

namespace App\Service;

use App\Service\OdbcService;
use App\Service\RequestOdbcService;
use Doctrine\ORM\EntityManagerInterface;

//class permettant de Requêter Rubis pour obtenir les codes tournée
//fait appel au service RequestOdbcService pour obtenir les requêtes
//fait appel au service OdbcService pour exécuter les requêtes
class TourCodeService
{
    private $requestOdbcService;
    private $odbcService;

    public function __construct(RequestOdbcService $requestOdbcService, OdbcService $odbcService)
    {
        $this->requestOdbcService = $requestOdbcService;
        $this->odbcService = $odbcService;
    }

    public function getCodeTour()
    {
        $sql = $this->requestOdbcService->getTourCodes();
        $results = $this->odbcService->executeQuery($sql);

        $csvFilePath = '/var/www/ArbaConnect/public/csv/tour_code/tour_code.csv';
        $this->writeToCsv($csvFilePath, $results);
    }

    private function writeToCsv(string $filePath, array $data)
    {
        // Vérifier si le fichier existe
        if (file_exists($filePath)) {
            // Si le fichier existe, le supprimer pour le recréer
            unlink($filePath);
        }

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
