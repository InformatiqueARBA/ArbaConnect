<?php

namespace App\Service;

use App\Entity\OrderDetail;
use App\Entity\Order;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CsvGeneratorService
{
    private $csvDirectoryDeliveryDate;


    public function __construct(ParameterBagInterface $params)
    {
        $this->csvDirectoryDeliveryDate = $params->get('csv_directory_delivery_date');
    }


    // TODO: finaliser le csv.
    public function deliveryDateCsv(Order $order)
    {

        $timestamp = date('_H:i:s');

        $header = [
            'SNOCLI', 'SNOBON', 'SNTA02', 'SNTC07', 'SNTPRO', 'SNTLIS', 'SNTLIA', 'SNTLIM', 'SNTLIJ'
        ];

        $orderId = $order->getId();

        ($order->getOrderStatus() == 'EDITED') ?  $orderStatus = 1 : $orderStatus = 0;


        $filePath = $this->csvDirectoryDeliveryDate . 'AC' . $orderId . '.csv';


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
            $orderStatus
        ];
        fputcsv($file, $data, ';');

        fclose($file);

        /* TODO: Créer le déplacement du fichier
                Dans un second temps créer une ordere Symfony et la tâche Cron associée
            */
    }
}
