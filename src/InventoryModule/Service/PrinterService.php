<?php

namespace App\InventoryModule\Service;

use FilesystemIterator;
use Symfony\Component\Process\Process;

class PrinterService
{

    public function PDFPrinter(string $printerName, $directory, $destinationDirectory)
    {


        $files = new FilesystemIterator($directory);


        if ($printerName == 'ARBA1_2') {
            foreach ($files as $file) {
                if (substr(basename($file), 0, 5) == 'AQA_M' && ctype_digit(substr(basename($file), 5, 1)) && $file->getExtension() === 'pdf') {

                    $printerName = 'AccueilARBA1bis';
                    // $printerName = 'Menuiserie';
                    // $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    // $process = new Process($command);
                    // $process->run();

                    if (file_exists($file)) {
                        rename($file, $destinationDirectory . basename($file));
                    }
                }




                if (!preg_match('/^AQA_M\d/', basename($file)) && $file->getExtension() === 'pdf') {

                    $printerName = 'AccueilARBA1bis';
                    $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    $process = new Process($command);
                    $process->run();

                    if (file_exists($file)) {
                        rename($file, $destinationDirectory . basename($file));
                    }
                }
            }
        } else {
            foreach ($files as $file) {
                if (!preg_match('/^AQA_M\d/', basename($file)) && $file->getExtension() === 'pdf') {

                    // $command = ['lp', '-d', $printerName, $file->getRealPath()];
                    // $process = new Process($command);
                    // $process->run();

                    if (file_exists($file)) {
                        rename($file, $destinationDirectory . basename($file));
                    }
                }
            }
        }
    }
}
