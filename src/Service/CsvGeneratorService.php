<?php

namespace App\Service;

use App\Entity\Acdb\Order;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CsvGeneratorService
{
    private $csvDirectoryDeliveryDate;
    private $csvToRubisService;
    private $csvSaveDirectory;


    public function __construct(ParameterBagInterface $params, CsvToRubisService $csvToRubisService)
    {
        $this->csvDirectoryDeliveryDate = $params->get('csv_directory_delivery_date');
        $this->csvSaveDirectory = $params->get('csv_save_directory');
        $this->csvToRubisService = $csvToRubisService;
    }


    // TODO: finaliser le csv.
    public function deliveryDateCsv(Order $order)
    {

        $timestamp = date('_H:i:s');

        $header = [
            'SNOCLI',
            'SNOBON',
            'SNTA02',
            'SNTC07',
            'SNTPRO',
            'SNTLIS',
            'SNTLIA',
            'SNTLIM',
            'SNTLIJ',
            'SNTROF'
        ];

        $orderId = $order->getId();

        ($order->getOrderStatus() == 'EDITED') ?  $orderStatus = 1 : $orderStatus = 0;


        $filePath = $this->csvDirectoryDeliveryDate . 'AC' . $orderId . '.csv';
        $fileName = basename($filePath);


        $file = fopen($filePath, 'w');

        if ($file === false) {
            throw new \Exception('Unable to create or open the file: ' . $filePath);
        }

        // création entête
        fputcsv($file, $header, ';');

        $corporationId = $order->getCorporation()->getId();
        $deliveryDateString = $order->getDeliveryDate()->format('d-m-Y');

        $data = [
            $corporationId,
            $orderId . $timestamp,
            '2',
            $orderId,
            'AC',
            substr($deliveryDateString, 6, 2),
            substr($deliveryDateString, 8, 2),
            substr($deliveryDateString, 3, 2),
            substr($deliveryDateString, 0, 2),
            'R'
        ];
        fputcsv($file, $data, ';');

        // Copie le CSV sur le QDLS RUBIS
        //TODO: Déplacer vers le dossier spécifique & mise en place de l'autowire
        $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryDeliveryDate, $fileName);

        // Déplace le CSV vers le dossier de sauvegarde
        $destinationDir = '/home/dave/Documents/ArbaConnect/save/csv/'; //$this->csvSaveDirectory;

        $destinationPath = $destinationDir . $fileName;

        if (rename($filePath, $destinationPath)) {
            echo "File moved successfully to $destinationPath.\n";
        } else {
            throw new \Exception('Failed to move the file to: ' . $destinationPath);
        }

        //TODO: Gérer la durée de conservation des fichiers sauvegardés 90 jours ?

        /* TODO: Créer le déplacement du fichier
                Dans un second temps créer une ordere Symfony et la tâche Cron associée
            */
    }
}
