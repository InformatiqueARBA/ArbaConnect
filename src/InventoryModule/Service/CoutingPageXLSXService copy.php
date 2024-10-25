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
    private $filePath = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets';
    private $PDFfilePath = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/';

    public function saveSpreadsheet(Spreadsheet $spreadsheet, string $filePath): void
    {
        // Utiliser le writer Xlsx pour enregistrer le fichier
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($filePath);
    }




    public function generateCountingXLSX(array $articlesByLocation, $location, string $filePath): PdfWriter
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

        $headers = [
            'Emplacement 1',
            'Emplacement 2',
            'Emplacement 3',
            'Code Article',
            'Désignation 1',
            'Désignation 2',
            'N° de lot',
            'Condi.',
            'Unité',
            'Qté empl. 1',
            'Qté empl. 2',
            'Qté empl. 3'
        ];

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']]
        ];

        // Cell style
        $cellStyle = [
            'font' => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        // Function to add headers
        $addHeaders = function ($sheet, $headerStyle, $headers, $rowIndex) {
            $columnIndex = 1;
            foreach ($headers as $header) {
                $cellCoordinate = Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex;
                $sheet->setCellValue($cellCoordinate, $header);
                $sheet->getStyle($cellCoordinate)->applyFromArray($headerStyle);
                $columnIndex++;
            }
        };

        // Set rows to repeat at top
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);

        // Add headers at the first row
        $addHeaders($sheet, $headerStyle, $headers, 1);

        $rowIndex = 2; // Start from row 2 since headers are at row 1

        // Loop through articles and fill the Excel file
        foreach ($articlesByLocation as $inventoryArticle) {
            // Fill data
            $data = [
                (!empty($inventoryArticle->getLocation()) && substr($inventoryArticle->getLocation(), 0, 5) === $location) ? $inventoryArticle->getLocation() : '',
                (!empty($inventoryArticle->getLocation2()) && substr($inventoryArticle->getLocation2(), 0, 5) === $location) ? $inventoryArticle->getLocation2() : '',
                (!empty($inventoryArticle->getLocation3()) && substr($inventoryArticle->getLocation3(), 0, 5) === $location) ? $inventoryArticle->getLocation3() : '',
                $inventoryArticle->getArticleCode(),
                $inventoryArticle->getDesignation1(),
                $inventoryArticle->getDesignation2(),
                $inventoryArticle->getLotCode(),
                $inventoryArticle->getPackaging(),
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
            $fillType = $rowIndex % 2 === 0 ? 'FFFFFFFF' : 'FFF2F2F2';
            $sheet->getStyle('A' . $rowIndex . ':' . Coordinate::stringFromColumnIndex(count($headers)) . $rowIndex)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($fillType);

            $rowIndex++;
        }

        // Auto-adjust column widths
        foreach (range(1, count($headers)) as $columnIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex))->setAutoSize(true);
        }

        // Increase row heights
        foreach ($sheet->getRowIterator() as $row) {
            $sheet->getRowDimension($row->getRowIndex())->setRowHeight(25);
        }

        $pdfWriter = new PdfWriter($spreadsheet);
        $pdfWriter->save($filePath);

        return $pdfWriter;
    }
}
