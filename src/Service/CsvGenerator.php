<?php

namespace App\Service;

use App\Entity\OrderDetail;
use App\Entity\Order;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CsvGenerator
{
    private $csvDirectoryDeliveryDate;


    public function __construct(ParameterBagInterface $params)
    {
        $this->csvDirectoryDeliveryDate = $params->get('csv_directory_delivery_date');
    }


    // TODO: finaliser le csv.
    public function deliveryDateCsv(Order $order)
    {



        $header = [
            'SNOCLI', 'SNOBON', 'SNTA02', 'SNTC07', 'SNTPRO', 'SNTROF', 'SNTAGE', 'SNTDEP',
            'SNTBOS', 'SNTBOA', 'SNTBOM', 'SNTBOJ', 'SNTVEN', 'SNTLIS', 'SNTLIA', 'SNTLIM',
            'SNTLIJ', 'SNTRFC', 'SNTRFS', 'SNTRFA', 'SNTRFM', 'SNTRFJ', 'SEOLIG', 'SCOLIG',
            'SENART', 'SENROF', 'SENTYP', 'SENQTE', 'SENLIS', 'SENLIA', 'SENLIM', 'SENLIJ',
            'SENREV', 'SENPRI', 'SENTCD', 'SENCSA', 'SENDS1', 'SNTTCD', 'SENMAJ', 'SENMIN',
            'SENB17', 'SENN05', 'SENC48', 'SENB41'
        ];

        $orderId = $order->getId();
        $filePath = $this->csvDirectoryDeliveryDate . 'AC' . $orderId . '.csv';


        $file = fopen($filePath, 'w');

        if ($file === false) {
            throw new \Exception('Unable to create or open the file: ' . $filePath);
        }

        // création entête
        fputcsv($file, $header, ';');




        $corporationId = $order->getCorporation()->getId();
        $orderDateString = $order->getOrderDate()->format('d-m-Y');
        $deliveryDateString = $order->getDeliveryDate()->format('d-m-Y');


        $data = [
            $corporationId,
            $orderId,
            '2',
            '',
            'AC',
            '', //'R',
            '', //'AQA',
            '', //'AQA',
            '', //substr($orderDateString, 6, 2),
            '', //substr($orderDateString, 8, 2),
            '', //substr($orderDateString, 3, 2),
            '', //substr($orderDateString, 0, 2),
            $order->getSeller(),
            substr($deliveryDateString, 6, 2),
            substr($deliveryDateString, 8, 2),
            substr($deliveryDateString, 3, 2),
            substr($deliveryDateString, 0, 2),
            '', //$order->getReference(),
            '', //substr($orderDateString, 6, 2),
            '', //substr($orderDateString, 8, 2),
            '', //substr($orderDateString, 3, 2),
            '', //substr($orderDateString, 0, 2),
            '', //$compteurligneFormatted,
            '', // '', // TODO: prendre en compte ligne com ajouter à l'entité
            '', // $orderDetail->getItemNumber(),
            '', // 'R',
            '', // '', // TODO: sentyp
            '', // $orderDetail->getQuantity(),
            substr($deliveryDateString, 6, 2),
            substr($deliveryDateString, 8, 2),
            substr($deliveryDateString, 3, 2),
            substr($deliveryDateString, 0, 2),






        ];
        fputcsv($file, $data, ';');

        fclose($file);

        /* TODO: Créer le déplacement du fichier
                Dans un second temps créer une ordere Symfony et la tâche Cron associée
            */
    }
}
