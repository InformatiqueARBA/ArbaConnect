<?php

namespace App\InventoryModule\Service;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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




    public function generateCountingXLSX(array $bob): Spreadsheet
    {
        // Créer un nouveau Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Localisation',
            'Code Produit',
            'Désignation 1',
            'Désignation 2',
            'N° de lot',
            'Quantité',
            'Un Prépa',
            'Dont Qte dép.',
            '% dépré.',
            'Compt 2',
        ];

        // Écriture des en-têtes dans la première ligne
        $columnIndex = 1; // Commence à la colonne A
        foreach ($headers as $header) {
            $cellCoordinate = Coordinate::stringFromColumnIndex($columnIndex) . '1';
            $sheet->setCellValue($cellCoordinate, $header);
            $columnIndex++;
        }

        // Écriture des données, en commençant par la ligne 2
        $rowIndex = 2;
        foreach ($bob as $inventoryArticle) {

            $data = [
                $inventoryArticle->getLocation(),          // INVW1
                $inventoryArticle->getArticleCode(),       // INVAR
                $inventoryArticle->getDesignation1(),
                $inventoryArticle->getDesignation2(),
                $inventoryArticle->getLotCode(),
                '',
                $inventoryArticle->getPreparationUnit(),
                '',
                '',
                ''
            ];

            // Écriture des données dans la ligne courante
            $columnIndex = 1;
            foreach ($data as $cellValue) {
                $cellCoordinate = Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex;
                $sheet->setCellValue($cellCoordinate, $cellValue);
                $columnIndex++;
            }
            $rowIndex++;
        }


        return $spreadsheet;
    }
}
