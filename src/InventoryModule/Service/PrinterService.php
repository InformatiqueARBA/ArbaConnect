<?php

namespace App\InventoryModule\Service;

use FilesystemIterator;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PrinterService
{
    private $filePath = '/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/';
    // private $printerName = 'Lexmark_MS810'; // Imprimante déclarées pour les tests : ['Lexmark_MS810','SalleExpo']

    public function PDFPrinter(string $printerName)
    {

        // dd($printerName);

        $fichierDestination = "/var/www/ArbaConnect/public/csv/inventory/counting_sheets/PDF/printed/";
        // Liste les fichiers du dossier
        $files = new FilesystemIterator($this->filePath);

        foreach ($files as $file) {
            // Si PDF impression
            if ($file->getExtension() === 'pdf') {

                // $command = ['lp', '-d', $this->printerName, $file->getRealPath()];
                // $process = new Process($command);
                // $process->run();

                // if (!$process->isSuccessful()) {
                //     throw new ProcessFailedException($process);
                // }
                if (file_exists($file)) {
                    if (rename($file, $fichierDestination  . basename($file)));
                    // dd($fichierDestination  . basename($file));
                }

                // Optionnel : Supprimer le fichier après impression
                //unlink($file->getRealPath());
            }
        }
    }
}
