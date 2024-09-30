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
}
