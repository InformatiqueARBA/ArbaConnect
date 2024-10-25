<?php

namespace App\InventoryModule\Service;

use FilesystemIterator;

class PrinterServiceCOUCOU
{
    private $filePath = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/';

    public function PDFPrinter(string $printerName)
    {
        //$printerName = 'Developpement';
        $fichierDestination = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/printed/";
        $files = new FilesystemIterator($this->filePath);

        if ($printerName == 'Menuiserie') {
            foreach ($files as $file) {
                if (substr(basename($file), 0, 5) == 'AQA_M' && ctype_digit(substr(basename($file), 5, 1)) && $file->getExtension() === 'pdf') {
                    // Affiche le nom du fichier au lieu d'imprimer physiquement
                    echo "Impression simulée : " . $file->getRealPath() . "\n";

                    if (file_exists($file)) {
                        rename($file, $fichierDestination . basename($file));
                    }
                }
            }
        } else {
            foreach ($files as $file) {
                if (!preg_match('/^AQA_M\d/', basename($file)) && $file->getExtension() === 'pdf') {
                    // Affiche le nom du fichier au lieu d'imprimer physiquement
                    echo "Impression simulée boucle ELSE : " . $file->getRealPath() . "\n";

                    if (file_exists($file)) {
                        rename($file, $fichierDestination . basename($file));
                    }
                }
            }
        }
    }

    public function printTestPDF(string $printerName)
    {
        $file = "/var/www/ArbaConnect/public/images/test.pdf";

        if (!file_exists($file)) {
            throw new \Exception("Le fichier test.pdf n'existe pas à l'emplacement spécifié.");
        }

        // Affiche le nom du fichier pour le test d'impression
        echo "Impression de test simulée : " . $file . " sur l'imprimante " . $printerName . "\n";

        return "Impression du fichier test.pdf réussie (simulation) sur l'imprimante " . $printerName;
    }
}
