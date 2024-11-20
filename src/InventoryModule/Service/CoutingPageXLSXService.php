<?php

namespace App\InventoryModule\Service;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf as PdfWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class CoutingPageXLSXService
{

    public function saveSpreadsheet(Spreadsheet $spreadsheet, string $filePath): void
    {
        // Utiliser le writer Xlsx pour enregistrer le fichier
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($filePath);
    }

    public function generateCountingXLSXStock(array $articlesByLocation, $location, string $filePath, $inventoryNumber): PdfWriter
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

        // Set minimal margins
        $sheet->getPageMargins()->setTop(0.18);
        $sheet->getPageMargins()->setRight(0.18);
        $sheet->getPageMargins()->setLeft(0.18);
        $sheet->getPageMargins()->setBottom(0.18);

        // Get current date in European format
        $currentDate = (new \DateTime())->format('d/m/Y');

        // Define the number of columns in the table to correctly merge cells for the header
        $totalColumns = 7; // Number of columns in the table (from A to G)

        // Merge the first row cells for the header
        $sheet->mergeCells('A1:' . Coordinate::stringFromColumnIndex($totalColumns) . '1');

        // Set the complete text in the merged cell
        $sheet->setCellValue('A1', 'Compté par :  ........ / .........' . str_repeat("\u{00A0}", 36) .  'Inventaire n° ' . $inventoryNumber . ' du ' . $currentDate . ' - Allée : ' . trim($location)  . str_repeat("\u{00A0}", 35) . '    Saisie par :  ..............');

        // Appliquer un style de police personnalisé à la cellule A1
        $headerFontStyle = [
            'font' => [
                'bold' => true,       // Mettre en gras
                'size' => 14,         // Taille de police 14
                'name' => 'Arial',    // Police Arial (ou toute autre police que vous préférez)
                'color' => ['argb' => 'FF000000'], // Couleur de police noire
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,

            ]
        ];

        // Appliquer le style à la cellule A1
        $sheet->getStyle('A1')->applyFromArray($headerFontStyle);

        // Adjust row height for better display
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add an empty row for spacing between the header and the table
        $rowIndex = 2;
        $sheet->getRowDimension($rowIndex)->setRowHeight(10); // Leave some space after the header

        // Move the data header to row 3
        $rowIndex = 3; // Start from row 3 for the table header

        // Header style for data columns
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']]
        ];

        // Cell style
        $cellStyle = [
            'font' => ['size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        // Définir une largeur fixe pour les colonnes Désignation 1 et Désignation 2
        $sheet->getColumnDimension('B')->setWidth(20); // Largeur fixe pour "Code Article"
        $sheet->getColumnDimension('C')->setWidth(60); // Largeur fixe pour "Désignation 1"
        $sheet->getColumnDimension('D')->setWidth(60); // Largeur fixe pour "Désignation 2"
        $sheet->getColumnDimension('F')->setWidth(20); // Largeur fixe pour "Conditionnement"

        // Headers for the data
        $headers = [
            'Emplacement',
            'Article',
            'Désignation 1',
            'Désignation 2',
            '  Quantité  ',
            'Condi.',
            'Dont qté dép.',
            '% dép.',
            'Recomptage'
        ];

        // Add headers for the data at row 3
        $this->addHeaders($sheet, $headerStyle, $headers, $rowIndex);

        // Set rows to repeat at top (for example rows 1 to 3)
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);

        $rowIndex = 4; // Start from row 4 for the actual data

        // Define pagination
        $currentPage = 1;
        $rowsPerPage = 32;
        $totalPages = ceil(count($articlesByLocation) / $rowsPerPage); // Calculate total pages based on rows per page

        // Loop through articles and fill the Excel file
        foreach ($articlesByLocation as $index => $inventoryArticle) {
            // Fill data
            $data = [
                $inventoryArticle->getLocation(),
                $inventoryArticle->getArticleCode(),
                $inventoryArticle->getDesignation1(),
                $inventoryArticle->getDesignation2(),
                '',
                // $inventoryArticle->getUnitCode(),
                $inventoryArticle->getPreparationUnit(),
                '',
                '',
                ''
            ];

            // Write data
            $columnIndex = 1;
            foreach ($data as $cellValue) {
                $cellCoordinate = Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex;
                $sheet->setCellValue($cellCoordinate, $cellValue);
                $sheet->getStyle($cellCoordinate)->applyFromArray($cellStyle);
                $columnIndex++;
            }

            // Alternate row styles
            // $fillType = $rowIndex % 2 === 0 ? 'FFFFFFFF' : 'FFF2F2F2';
            $fillType = $rowIndex % 2 === 0 ? 'FFFFFFFF' : 'FFCCCCCC'; // Blanc et gris plus foncé
            $sheet->getStyle('A' . $rowIndex . ':' . Coordinate::stringFromColumnIndex(count($headers)) . $rowIndex)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($fillType);

            $rowIndex++;

            // Add footer for page number after each 30 rows
            if (($index + 1) % $rowsPerPage == 0) {
                // Add page footer
                $sheet->mergeCells('A' . $rowIndex . ':G' . $rowIndex);
                $sheet->setCellValue('A' . $rowIndex, 'Page ' . $currentPage . '/' . $totalPages);
                $sheet->getStyle('A' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $currentPage++;
                $rowIndex++; // Move to the next row
            }
        }

        // If the last page doesn't have 30 rows, add the footer manually
        if (($rowIndex - 4) % $rowsPerPage != 0) {
            $sheet->mergeCells('A' . $rowIndex . ':G' . $rowIndex);
            $sheet->setCellValue('A' . $rowIndex, 'Page ' . $currentPage . '/' . $totalPages);
            $sheet->getStyle('A' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Auto-adjust column widths, except for columns C and D
        foreach (range(1, count($headers)) as $columnIndex) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
            if (!in_array($columnLetter, ['B', 'C', 'D', 'F'])) {
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            }
        }

        // Increase row heights
        foreach ($sheet->getRowIterator() as $row) {
            $sheet->getRowDimension($row->getRowIndex())->setRowHeight(25);
        }

        // Save to PDF
        $pdfWriter = new PdfWriter($spreadsheet);
        $pdfWriter->save($filePath);

        return $pdfWriter;
    }







    public function generateCountingXLSXLot(array $articlesByLocation, $location, string $filePath, $inventoryNumber): PdfWriter
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

        // Set minimal margins
        $sheet->getPageMargins()->setTop(0.18);
        $sheet->getPageMargins()->setRight(0.18);
        $sheet->getPageMargins()->setLeft(0.18);
        $sheet->getPageMargins()->setBottom(0.18);

        // Get current date in European format
        $currentDate = (new \DateTime())->format('d/m/Y');

        // Define the number of columns in the table to correctly merge cells for the header
        $totalColumns = 7; // Number of columns in the table (from A to G)

        // Merge the first row cells for the header
        $sheet->mergeCells('A1:' . Coordinate::stringFromColumnIndex($totalColumns) . '1');

        // Set the complete text in the merged cell
        $sheet->setCellValue('A1', 'Compté par :  ........ / .........' . str_repeat("\u{00A0}", 36) .  'Inventaire n° ' . $inventoryNumber . ' du ' . $currentDate . ' - Allée : ' . trim($location)  . str_repeat("\u{00A0}", 35) . '    Saisie par :  ..............');

        // Appliquer un style de police personnalisé à la cellule A1
        $headerFontStyle = [
            'font' => [
                'bold' => true,       // Mettre en gras
                'size' => 14,         // Taille de police 14
                'name' => 'Arial',    // Police Arial (ou toute autre police que vous préférez)
                'color' => ['argb' => 'FF000000'], // Couleur de police noire
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,

            ]
        ];

        // Appliquer le style à la cellule A1
        $sheet->getStyle('A1')->applyFromArray($headerFontStyle);

        // Adjust row height for better display
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add an empty row for spacing between the header and the table
        $rowIndex = 2;
        $sheet->getRowDimension($rowIndex)->setRowHeight(10); // Leave some space after the header

        // Move the data header to row 3
        $rowIndex = 3; // Start from row 3 for the table header

        // Header style for data columns
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']]
        ];

        // Cell style
        $cellStyle = [
            'font' => ['size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        // Définir une largeur fixe pour les colonnes Désignation 1 et Désignation 2
        $sheet->getColumnDimension('B')->setWidth(20); // Largeur fixe pour "Code Article"
        $sheet->getColumnDimension('C')->setWidth(60); // Largeur fixe pour "Désignation 1"
        $sheet->getColumnDimension('D')->setWidth(60); // Largeur fixe pour "Désignation 2"
        $sheet->getColumnDimension('F')->setWidth(20); // Largeur fixe pour "Conditionnement"

        // Headers for the data
        $headers = [
            'Emplacement',
            'Article',
            'Désignation 1',
            'Désignation 2',
            'N° de lot',
            '  Quantité  ',
            'Condi.',
            'Dont qté dép.',
            '% dép.',
            'Recomptage'
        ];

        // Add headers for the data at row 3
        $this->addHeaders($sheet, $headerStyle, $headers, $rowIndex);

        // Set rows to repeat at top (for example rows 1 to 3)
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);

        $rowIndex = 4; // Start from row 4 for the actual data

        // Define pagination
        $currentPage = 1;
        $rowsPerPage = 35;
        $totalPages = ceil(count($articlesByLocation) / $rowsPerPage); // Calculate total pages based on rows per page

        // Loop through articles and fill the Excel file
        foreach ($articlesByLocation as $index => $inventoryArticle) {
            // Fill data
            $data = [
                $inventoryArticle->getLocation(),
                $inventoryArticle->getArticleCode(),
                $inventoryArticle->getDesignation1(),
                $inventoryArticle->getDesignation2(),
                $inventoryArticle->getLotCode(),
                '',
                // $inventoryArticle->getUnitCode(),
                $inventoryArticle->getPreparationUnit(),
                '',
                '',
                ''
            ];

            // Write data
            $columnIndex = 1;
            foreach ($data as $cellValue) {
                $cellCoordinate = Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex;
                $sheet->setCellValue($cellCoordinate, $cellValue);
                $sheet->getStyle($cellCoordinate)->applyFromArray($cellStyle);
                $columnIndex++;
            }

            // Alternate row styles
            // $fillType = $rowIndex % 2 === 0 ? 'FFFFFFFF' : 'FFF2F2F2';
            $fillType = $rowIndex % 2 === 0 ? 'FFFFFFFF' : 'FFCCCCCC'; // Blanc et gris plus foncé
            $sheet->getStyle('A' . $rowIndex . ':' . Coordinate::stringFromColumnIndex(count($headers)) . $rowIndex)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($fillType);

            $rowIndex++;

            // Add footer for page number after each 30 rows
            if (($index + 1) % $rowsPerPage == 0) {
                // Add page footer
                $sheet->mergeCells('A' . $rowIndex . ':G' . $rowIndex);
                $sheet->setCellValue('A' . $rowIndex, 'Page ' . $currentPage . '/' . $totalPages);
                $sheet->getStyle('A' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $currentPage++;
                $rowIndex++; // Move to the next row
            }
        }

        // If the last page doesn't have 30 rows, add the footer manually
        if (($rowIndex - 4) % $rowsPerPage != 0) {
            $sheet->mergeCells('A' . $rowIndex . ':G' . $rowIndex);
            $sheet->setCellValue('A' . $rowIndex, 'Page ' . $currentPage . '/' . $totalPages);
            $sheet->getStyle('A' . $rowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Auto-adjust column widths, except for columns C and D
        foreach (range(1, count($headers)) as $columnIndex) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
            if (!in_array($columnLetter, ['B', 'C', 'D', 'F'])) {
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            }
        }

        // Increase row heights
        foreach ($sheet->getRowIterator() as $row) {
            $sheet->getRowDimension($row->getRowIndex())->setRowHeight(25);
        }

        // Save to PDF
        $pdfWriter = new PdfWriter($spreadsheet);
        $pdfWriter->save($filePath);

        return $pdfWriter;
    }


    /**
     * Helper method to add headers to the sheet
     */
    private function addHeaders($sheet, $headerStyle, $headers, $rowIndex)
    {
        $columnIndex = 1;
        foreach ($headers as $header) {
            $cellCoordinate = Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex;
            $sheet->setCellValue($cellCoordinate, $header);
            $sheet->getStyle($cellCoordinate)->applyFromArray($headerStyle);
            $columnIndex++;
        }
    }
}
