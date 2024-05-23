<?php

namespace App\Service;

use App\Entity\OrderDetail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CsvGenerator
{
    private $csvDirectoryDeliveryDate;


    public function __construct(ParameterBagInterface $params)
    {
        $this->csvDirectoryDeliveryDate = $params->get('csv_directory_delivery_date');
    }


    // TODO: finaliser le csv.
    public function deliveryDateCsv(array $orderDetails)
    {
        // dd($orderDetails);

        $orderDetail = $orderDetails[0];

        $header = [
            'SNOCLI', 'SNOBON', 'SNTA02', 'SNTC07', 'SNTPRO', 'SNTROF', 'SNTAGE', 'SNTDEP',
            'SNTBOS', 'SNTBOA', 'SNTBOM', 'SNTBOJ', 'SNTVEN', 'SNTLIS', 'SNTLIA', 'SNTLIM',
            'SNTLIJ', 'SNTRFC', 'SNTRFS', 'SNTRFA', 'SNTRFM', 'SNTRFJ', 'SEOLIG', 'SCOLIG',
            'SENART', 'SENROF', 'SENTYP', 'SENQTE', 'SENLIS', 'SENLIA', 'SENLIM', 'SENLIJ',
            'SENREV', 'SENPRI', 'SENTCD', 'SENCSA', 'SENDS1', 'SNTTCD', 'SENMAJ', 'SENMIN',
            'SENB17', 'SENN05', 'SENC48', 'SENB41'
        ];

        $commandId = $orderDetail->getCommand()->getId();
        $filePath = $this->csvDirectoryDeliveryDate . 'AC' . $commandId . '.csv';


        $file = fopen($filePath, 'w');

        if ($file === false) {
            throw new \Exception('Unable to create or open the file: ' . $filePath);
        }

        // création entête
        fputcsv($file, $header, ';');


        // création de la ligne suppression
        $command = $orderDetail->getCommand();
        $corporationId = $command->getCorporation()->getId();
        $data = [
            $corporationId,
            $commandId . '/D',
            '4',
            $commandId,
            'AC',
            'R'

        ];
        fputcsv($file, $data, ';');


        // création ligne article
        $compteurligne = 0;
        foreach ($orderDetails as $orderDetail) {
            $command = $orderDetail->getCommand();
            $corporationId = $command->getCorporation()->getId();
            $orderDateString = $command->getOrderDate()->format('d-m-Y');
            $deliveryDateString = $command->getDeliveryDate()->format('d-m-Y');
            $compteurligne++;
            // Formatage du compteur de ligne en une chaîne de 3 caractères avec des zéros à gauche
            $compteurligneFormatted = str_pad($compteurligne, 3, '0', STR_PAD_LEFT);
            // dd(substr($orderDateString, 6, 2));
            $data = [
                $corporationId,
                'AC' . $commandId,
                'C',
                'AC',
                'R',
                'AQA',
                'AQA',
                substr($orderDateString, 6, 2),
                substr($orderDateString, 8, 2),
                substr($orderDateString, 3, 2),
                substr($orderDateString, 0, 2),
                'AC',
                substr($deliveryDateString, 6, 2),
                substr($deliveryDateString, 8, 2),
                substr($deliveryDateString, 3, 2),
                substr($deliveryDateString, 0, 2),
                $command->getReference(),
                substr($orderDateString, 6, 2),
                substr($orderDateString, 8, 2),
                substr($orderDateString, 3, 2),
                substr($orderDateString, 0, 2),
                $compteurligneFormatted,
                '', // TODO: prendre en compte ligne com ajouter à l'entité
                $orderDetail->getItemNumber(),
                'R',
                '', // TODO: sentyp
                $orderDetail->getQuantity(),
                substr($deliveryDateString, 6, 2),
                substr($deliveryDateString, 8, 2),
                substr($deliveryDateString, 3, 2),
                substr($deliveryDateString, 0, 2),






            ];
            fputcsv($file, $data, ';');
        }
        fclose($file);


        dd($filePath);
    }
}
