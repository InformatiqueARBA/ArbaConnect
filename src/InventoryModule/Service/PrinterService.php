<?php

namespace App\InventoryModule\Service;

use FilesystemIterator;
use Symfony\Component\Process\Process;

class PrinterService
{
    private $filePath = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/';

    public function PDFPrinter(string $printerName)
    {

        $fichierDestination = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/printed/";
        $files = new FilesystemIterator($this->filePath);


        if ($printerName == 'ARBA1_2') {
            foreach ($files as $file) {
                if (substr(basename($file), 0, 5) == 'AQA_M' && ctype_digit(substr(basename($file), 5, 1)) && $file->getExtension() === 'pdf') {


                    // Affiche le nom du fichier au lieu d'imprimer physiquement
                    // echo "Impression simulée : " . $file->getRealPath() . "\n";
                    // TODO: à supprimer
                    $printerName = 'Menuiserie';
                    // $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    // $process = new Process($command);
                    // $process->run();

                    if (file_exists($file)) {
                        rename($file, $fichierDestination . basename($file));
                    }
                }




                if (!preg_match('/^AQA_M\d/', basename($file)) && $file->getExtension() === 'pdf') {
                    // Affiche le nom du fichier au lieu d'imprimer physiquement
                    // echo "Impression simulée boucle ELSE : " . $file->getRealPath() . "\n";
                    // TODO: à supprimer
                    $printerName = 'AccueilARBA1bis';
                    // $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    // $process = new Process($command);
                    // $process->run();

                    if (file_exists($file)) {
                        rename($file, $fichierDestination . basename($file));
                    }
                }
            }
        } else {
            foreach ($files as $file) {
                if (!preg_match('/^AQA_M\d/', basename($file)) && $file->getExtension() === 'pdf') {
                    // Affiche le nom du fichier au lieu d'imprimer physiquement
                    // echo "Impression simulée boucle ELSE : " . $file->getRealPath() . "\n";

                    // $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    // $process = new Process($command);
                    // $process->run();

                    if (file_exists($file)) {
                        rename($file, $fichierDestination . basename($file));
                    }
                }
            }
        }
    }
}
