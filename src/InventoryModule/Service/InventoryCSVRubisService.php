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
    private $ERPDirProdINV;


    public function __construct(ParameterBagInterface $params, CsvToRubisService $csvToRubisService)
    {
        $this->csvDirectoryInventory = $params->get('csv_directory_inventory');
        $this->csvSaveDirectoryInventory = $params->get('csv_save_directory_inventory');
        $this->csvToRubisService = $csvToRubisService;
        $this->ERPDirProdINV = $params->get('erp_dir_prod_inv');
    }


    // TODO: finaliser le csv.
    public function inventoryCsv(InventoryArticle $inventoryArticle)
    {

        $timestamp = date('_H:i:s');

        $header = [
            'INVNO',  // Inventaire
            'INVDP',  // Dépôt
            'INVW1',  // Bordereau
            'INVID',  // Identifiant
            'INLIE',  // Description
            'INVAR',  // Article
            'HILOT',  // Lot
            'INVSN',  // Nombre
            'INVSC',  // Conditionnement
            'INVQS',  // QTE en US
            'INVSU',  // Unité
            'INVL1'   // 'N' = Génération d'inventaire à non.
        ];

        // Définition du chemin du fichier CSV
        // $filePath = $this->csvDirectoryInventory . 'Inventory_' . $inventoryArticle->getInventoryNumber() . $timestamp . '.csv';
        // TODO: changer le npm du csv pour qu'il corresponde à RUBIS 
        $filePath = $this->csvDirectoryInventory . 'Inv_'  . '.csv';
        $fileName = basename($filePath);

        // Création du fichier CSV
        $file = fopen($filePath, 'w');

        if ($file === false) {
            throw new \Exception('Impossible de créer ou d\'ouvrir le fichier : ' . $filePath);
        }

        // Écriture de l'en-tête dans le fichier CSV
        fputcsv($file, $header, ';');

        // Récupération des données de l'entité pour remplir chaque colonne
        $data = [
            $inventoryArticle->getInventoryNumber(),   // INVNO
            $inventoryArticle->getWarehouse(),         // INVDP
            $inventoryArticle->getLocation(),          // INVW1
            'INV_GEN',                                 // INVID
            'Inventaire test',                         // INLIE
            $inventoryArticle->getArticleCode(),       // INVAR
            $inventoryArticle->getLotCode(),           // HILOT
            $inventoryArticle->getQuantityLocation1(), // INVSN ce champ et les 2 prochains c'est à vérifier
            $inventoryArticle->getPackaging(),         // INVSC
            $inventoryArticle->getQuantityLocation1(), // INVQS
            $inventoryArticle->getPreparationUnit(),   // INVSU
            'N'                                        // INVL1
        ];

        // Écriture des données dans le fichier CSV
        fputcsv($file, $data, ';');

        // Fermeture du fichier
        fclose($file);
        // dd($this->csvDirectoryInventory, $fileName, $this->ERPDirProdINV);

        // Copie le CSV vers le QDLS
        $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryInventory, $fileName, $this->ERPDirProdINV);

        // Déplace le fichier CSV vers le dossier de sauvegarde
        //$this->moveCsvToSaveDirectory($fileName);

        return [
            'header' => $header,
            'data' => $data
        ];
    }



    public function inventoryCsvArray(array $inventoryArticleByLoca)
    {
        $timestamp = date('_H:i:s');

        $header = [
            'INVNO',  // Inventaire
            'INVDP',  // Dépôt
            'INVW1',  // Bordereau
            'INVID',  // Identifiant
            'INLIE',  // Description
            'INVAR',  // Article
            'HILOT',  // Lot
            'INVSN',  // Nombre
            'INVSC',  // Conditionnement
            'INVQS',  // QTE en US
            'INVSU',  // Unité
            'INVL1'   // 'N' = Génération d'inventaire à non.
        ];

        // Définition du chemin du fichier CSV
        // TODO: changer le nom du csv pour qu'il corresponde à RUBIS
        $filePath = $this->csvDirectoryInventory . 'Inv_' . '.csv';
        $fileName = basename($filePath);

        // Création du fichier CSV
        $file = fopen($filePath, 'w');

        if ($file === false) {
            throw new \Exception('Impossible de créer ou d\'ouvrir le fichier : ' . $filePath);
        }

        // Écriture de l'en-tête dans le fichier CSV
        fputcsv($file, $header, ';');

        // Boucle à travers chaque article dans l'inventaire
        foreach ($inventoryArticleByLoca as $inventoryArticle) {
            // Récupération des données de l'entité pour remplir chaque colonne
            $data = [
                $inventoryArticle->getInventoryNumber(),   // INVNO
                $inventoryArticle->getWarehouse(),         // INVDP
                $inventoryArticle->getLocation(),          // INVW1
                'INV_GEN',                                 // INVID
                'Inventaire test',                         // INLIE
                $inventoryArticle->getArticleCode(),       // INVAR
                $inventoryArticle->getLotCode(),           // HILOT
                $inventoryArticle->getQuantityLocation1(), // INVSN
                $inventoryArticle->getPackaging(),         // INVSC
                $inventoryArticle->getQuantityLocation1(), // INVQS
                $inventoryArticle->getPreparationUnit(),   // INVSU
                'N'                                        // INVL1
            ];

            // Écriture des données dans le fichier CSV
            fputcsv($file, $data, ';');
        }

        // Fermeture du fichier
        fclose($file);

        // Copie le CSV vers le QDLS
        $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryInventory, $fileName, $this->ERPDirProdINV);

        // Déplace le fichier CSV vers le dossier de sauvegarde (si nécessaire)
        //$this->moveCsvToSaveDirectory($fileName);

        return [
            'header' => $header,
            'fileName' => $fileName
        ];
    }
}
