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




    // fonction pour tester l'impression
    public function printTestPDF(string $printerName)
    {

        $file = "/var/www/ArbaConnect/public/images/test.pdf";

        // Vérifier si le fichier existe
        if (!file_exists($file)) {
            throw new \Exception("Le fichier test.pdf n'existe pas à l'emplacement spécifié.");
        }

        // Commande pour imprimer le fichier
        $command = ['lp', '-d', $printerName, $file];

        // Créer un processus pour exécuter la commande d'impression
        $process = new Process($command);
        $process->run();

        // Vérifier si le processus a réussi
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Retourner un message de succès
        return "Impression du fichier test.pdf réussie sur l'imprimante " . $printerName;
    }
}
