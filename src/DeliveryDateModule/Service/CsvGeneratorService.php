<?php

namespace App\DeliveryDateModule\Service;

use App\ArbaConnect\Service\CsvToRubisService;
use App\Entity\Acdb\Order;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CsvGeneratorService
{
    private $csvDirectoryDeliveryDate;
    private $csvToRubisService;
    private $csvSaveDirectoryDeliveryDate;
    private $ERPDirProdAC;


    public function __construct(ParameterBagInterface $params, CsvToRubisService $csvToRubisService)
    {
        $this->csvDirectoryDeliveryDate = $params->get('csv_directory_delivery_date');
        $this->csvSaveDirectoryDeliveryDate = $params->get('csv_save_directory_delivery_date');
        $this->csvToRubisService = $csvToRubisService;
        $this->ERPDirProdAC = $params->get('erp_dir_prod_ac');
    }


    // TODO: finaliser le csv.
    public function deliveryDateCsv(Order $order)
    {

        $timestamp = date('_H:i:s');
        $orderType = $order->getType();

        //Génère l'entête du fichier CSV avec le type de bon si une ORA a été modifiée
        if ($orderType == 'ORC') {
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
                'SNTB40',
                'SNTROF'
            ];
        } else {
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
        }

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

        //Génère les lignes du fichier CSV avec le type de bon si une ORA a été modifiée
        if ($orderType == 'ORC') {
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
                'ORC',
                'R'
            ];
        } else {
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
        }
        fputcsv($file, $data, ';');

        // Copie le CSV sur le QDLS RUBIS
        //TODO: Déplacer vers le dossier spécifique & mise en place de l'autowire
        $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryDeliveryDate, $fileName, $this->ERPDirProdAC);

        // Déplace le CSV vers le dossier de sauvegarde
        $destinationDir = $this->csvSaveDirectoryDeliveryDate;

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
