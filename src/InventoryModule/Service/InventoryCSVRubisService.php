<?php

namespace App\InventoryModule\Service;

use App\ArbaConnect\Service\CsvToRubisService;
use App\Entity\Security\InventoryArticle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InventoryCSVRubisService
{
    private $csvDirectoryInventory;
    private $csvToRubisService;
    private $csvSaveDirectoryInventory;


    public function __construct(ParameterBagInterface $params, CsvToRubisService $csvToRubisService)
    {
        $this->csvDirectoryInventory = $params->get('csv_directory_inventory');
        $this->csvSaveDirectoryInventory = $params->get('csv_save_directory_inventory');
        $this->csvToRubisService = $csvToRubisService;
    }


    // TODO: finaliser le csv.
    public function inventoryCsv(InventoryArticle $inventoryArticle)
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

        // $orderId = $order->getId();

        // ($order->getOrderStatus() == 'EDITED') ?  $orderStatus = 1 : $orderStatus = 0;


        // $filePath = $this->csvDirectoryInventory . 'AC' . $orderId . '.csv';
        // $fileName = basename($filePath);


        // $file = fopen($filePath, 'w');

        // if ($file === false) {
        //     throw new \Exception('Unable to create or open the file: ' . $filePath);
        // }

        // // création entête
        // fputcsv($file, $header, ';');

        // $corporationId = $order->getCorporation()->getId();
        // $deliveryDateString = $order->getDeliveryDate()->format('d-m-Y');

        // $data = [
        //     $corporationId,
        //     $orderId . $timestamp,
        //     '2',
        //     $orderId,
        //     'AC',
        //     substr($deliveryDateString, 6, 2),
        //     substr($deliveryDateString, 8, 2),
        //     substr($deliveryDateString, 3, 2),
        //     substr($deliveryDateString, 0, 2),
        //     'R'
        // ];
        // fputcsv($file, $data, ';');

        // Copie le CSV sur le QDLS RUBIS
        //TODO: Déplacer vers le dossier spécifique & mise en place de l'autowire
        // $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryInventory, $fileName);

        // // Déplace le CSV vers le dossier de sauvegarde
        // $destinationDir = $this->csvSaveDirectoryInventory;

        // $destinationPath = $destinationDir . $fileName;

        // if (rename($filePath, $destinationPath)) {
        //     echo "File moved successfully to $destinationPath.\n";
        // } else {
        //     throw new \Exception('Failed to move the file to: ' . $destinationPath);
        // }

        //TODO: Gérer la durée de conservation des fichiers sauvegardés 90 jours ?

        /* TODO: Créer le déplacement du fichier
                Dans un second temps créer une ordere Symfony et la tâche Cron associée
            */
    }
}
