<?php

namespace App\InventoryModule\Service;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CoutingPageXLSXService
{
    private $filePath = '/var/www/ArbaConnect/public/csv/inventory';

    public function generateXlsx(array $data): Spreadsheet
    {
        // Créer un nouveau Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Ajouter des données dans le fichier Excel
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Prénom');

        // Remplir les données depuis le tableau $data
        $row = 2; // Début des données à la ligne 2
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['nom']);
            $sheet->setCellValue('B' . $row, $item['prenom']);
            $row++;
        }

        // Retourne l'objet Spreadsheet pour plus de flexibilité
        return $spreadsheet;
    }

    public function saveSpreadsheet(Spreadsheet $spreadsheet, string $filePath): void
    {
        // Utiliser le writer Xlsx pour enregistrer le fichier
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($filePath);
    }


    public function generateXlsxYO(array $data): Spreadsheet
    {
        // Créer un nouveau Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $requestOdbcInventoryService = new RequestOdbcInventoryService();

        // $sql = $requestOdbcInventoryService->getArticlesWithLocation($inventoryNumber);
        // $results = $this->odbcService->executeQuery($sql);

        // foreach ($results as $result) {
        //     // Vérifie si l'INVENTORY_NUMBER existe déjà dans la base de données
        //     // $existingArticle = $this->em->getRepository(InventoryArticle::class)
        //     //   ->findOneBy(['inventoryNumber' => $result['INVENTORY_NUMBER']]);

        //     //if (!$existingArticle) {
        //     // Si l'INVENTORY_NUMBER n'existe pas, on crée un nouvel article
        //     $inventoryArticle = new InventoryArticle();
        //     $inventoryArticle->setInventoryNumber($result['INVENTORY_NUMBER']);
        //     $inventoryArticle->setWarehouse($result['WAREHOUSE']);
        //     $inventoryArticle->setLocation($result['LOCATION']);
        //     $inventoryArticle->setLocation2($result['LOCATION2']);
        //     $inventoryArticle->setLocation3($result['LOCATION3']);
        //     $inventoryArticle->setArticleCode($result['CODE_ARTICLE']);
        //     $inventoryArticle->setDesignation1($result['DESIGNATION1']);
        //     $inventoryArticle->setDesignation2($result['DESIGNATION2']);
        //     $inventoryArticle->setLotCode($result['CODE_LOT']);
        //     $inventoryArticle->setDimensionType($result['TYPE_DIMENSION']);
        //     $inventoryArticle->setPackaging($result['CONDITIONNEMENT']);
        //     $inventoryArticle->setPackagingName($result['LIBELLE_CONDI']);
        //     $inventoryArticle->setQuantityLocation1($result['QUANTITE_LOC1']);
        //     $inventoryArticle->setQuantityLocation2($result['QUANTITE_LOC2']);
        //     $inventoryArticle->setQuantityLocation3($result['QUANTITE_LOC3']);
        //     $inventoryArticle->setPreparationUnit($result['UNITE_PREPARATION']);
        //     $inventoryArticle->setQuantity2Location1($result['QUANTITE2_LOC1']);
        //     $inventoryArticle->setQuantity2Location2($result['QUANTITE2_LOC2']);
        //     $inventoryArticle->setQuantity2Location3($result['QUANTITE2_LOC3']);

        //     $this->em->persist($inventoryArticle);
        //     //} else {
        //     // Vous pouvez ajouter une action si l'article existe déjà, si nécessaire
        //     //}
        // }

        // Ajouter des données dans le fichier Excel
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Prénom');

        // Remplir les données depuis le tableau $data
        $row = 2; // Début des données à la ligne 2
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['nom']);
            $sheet->setCellValue('B' . $row, $item['prenom']);
            $row++;
        }

        // Retourne l'objet Spreadsheet pour plus de flexibilité
        return $spreadsheet;
    }

    // public function saveSpreadsheet(Spreadsheet $spreadsheet, string $filePath): void
    // {
    //     // Utiliser le writer Xlsx pour enregistrer le fichier
    //     $writer = new XlsxWriter($spreadsheet);
    //     $writer->save($filePath);
    // }
}
