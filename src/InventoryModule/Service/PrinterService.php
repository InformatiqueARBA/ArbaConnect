<?php

namespace App\InventoryModule\Service;

use FilesystemIterator;
use Symfony\Component\Process\Process;

class PrinterService
{

    public function PDFPrinter(string $printerName, $directory, $destinationDirectory)
    {


        $files = iterator_to_array(new FilesystemIterator($directory));
        usort($files, function ($a, $b) {
            return strcmp($a->getFilename(), $b->getFilename());
        });


        if ($printerName == 'ARBA1_2') {
            foreach ($files as $file) {

                if (substr(basename($file), 4, 5) == 'AQA_M' && $file->getExtension() === 'pdf') {


                    $printerName = 'Menuiserie';
                    $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    $process = new Process($command);
                    $process->run();

                    if (file_exists($file)) {
                        rename($file, $destinationDirectory . basename($file));
                    }



                    // mode debug permet de logger les commandes envoyées à l'imprimante
                    // $simulatedCommand = "lp -d $printerName " . $file->getRealPath() . "\n";
                    // file_put_contents('/var/www/ArbaConnect/public/csv/print_simulation.txt', $simulatedCommand, FILE_APPEND);
                    // if (file_exists($file)) {
                    //     rename($file, $destinationDirectory . basename($file));
                    // }
                }




                if (!preg_match('/^AQA_M\d/', basename($file)) && $file->getExtension() === 'pdf') {

                    $printerName = 'AccueilARBA1bis';
                    $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    $process = new Process($command);
                    $process->run();

                    if (file_exists($file)) {
                        rename($file, $destinationDirectory . basename($file));
                    }

                    // mode debug permet de logger les commandes envoyées à l'imprimante
                    // $simulatedCommand = "lp -d $printerName " . $file->getRealPath() . "\n";
                    // file_put_contents('/var/www/ArbaConnect/public/csv/print_simulation.txt', $simulatedCommand, FILE_APPEND);
                    // if (file_exists($file)) {
                    //     rename($file, $destinationDirectory . basename($file));
                    // }
                }
            }
        } else {
            foreach ($files as $file) {
                if (!preg_match('/^AQA_M\d/', basename($file)) && $file->getExtension() === 'pdf') {

                    $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    $process = new Process($command);
                    $process->run();

                    if (file_exists($file)) {
                        rename($file, $destinationDirectory . basename($file));
                    }
                }
            }

            // mode debug permet de logger les commandes envoyées à l'imprimante
            // foreach ($files as $file) {
            //     $simulatedCommand = "lp -d $printerName " . $file->getRealPath() . "\n";
            //     file_put_contents('/var/www/ArbaConnect/public/csv/print_simulation.txt', $simulatedCommand, FILE_APPEND);
            //     if (file_exists($file)) {
            //         rename($file, $destinationDirectory . basename($file));
            //     }
            // }
        }
    }
}
