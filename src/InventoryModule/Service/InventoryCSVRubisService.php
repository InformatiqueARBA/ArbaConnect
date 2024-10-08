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


    // // TODO: finaliser le csv.
    // public function inventoryCsv(InventoryArticle $inventoryArticle)
    // {

    //     $timestamp = date('_H:i:s');

    //     $header = [
    //         'INVNO',  // Inventaire
    //         'INVDP',  // Dépôt
    //         'INVW1',  // Bordereau
    //         'INVID',  // Identifiant
    //         'INLIE',  // Description
    //         'INVAR',  // Article
    //         'HILOT',  // Lot
    //         'INVSN',  // Nombre
    //         'INVSC',  // Conditionnement
    //         'INVQS',  // QTE en US
    //         'INVSU',  // Unité
    //         'INVL1'   // 'N' = Génération d'inventaire à non.
    //     ];

    //     // Définition du chemin du fichier CSV
    //     // $filePath = $this->csvDirectoryInventory . 'Inventory_' . $inventoryArticle->getInventoryNumber() . $timestamp . '.csv';
    //     // TODO: changer le npm du csv pour qu'il corresponde à RUBIS 
    //     $filePath = $this->csvDirectoryInventory . "I_$inventoryNumber.csv";
    //     $fileName = basename($filePath);

    //     // Création du fichier CSV
    //     $file = fopen($filePath, 'w');

    //     if ($file === false) {
    //         throw new \Exception('Impossible de créer ou d\'ouvrir le fichier : ' . $filePath);
    //     }

    //     // Écriture de l'en-tête dans le fichier CSV
    //     fputcsv($file, $header, ';');

    //     // Récupération des données de l'entité pour remplir chaque colonne
    //     $data = [
    //         $inventoryArticle->getInventoryNumber(),   // INVNO
    //         $inventoryArticle->getWarehouse(),         // INVDP
    //         $inventoryArticle->getLocation(),          // INVW1
    //         'INV_GEN',                                 // INVID
    //         'Inventaire test',                         // INLIE
    //         $inventoryArticle->getArticleCode(),       // INVAR
    //         $inventoryArticle->getLotCode(),           // HILOT
    //         $inventoryArticle->getQuantityLocation1(), // INVSN ce champ et les 2 prochains c'est à vérifier
    //         $inventoryArticle->getPackaging(),         // INVSC
    //         $inventoryArticle->getQuantityLocation1(), // INVQS
    //         $inventoryArticle->getPreparationUnit(),   // INVSU
    //         'N'                                        // INVL1
    //     ];

    //     // Écriture des données dans le fichier CSV
    //     fputcsv($file, $data, ';');

    //     // Fermeture du fichier
    //     fclose($file);
    //     // dd($this->csvDirectoryInventory, $fileName, $this->ERPDirProdINV);

    //     // Copie le CSV vers le QDLS
    //     $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryInventory, $fileName, $this->ERPDirProdINV);

    //     // Déplace le fichier CSV vers le dossier de sauvegarde
    //     //$this->moveCsvToSaveDirectory($fileName);

    //     return [
    //         'header' => $header,
    //         'data' => $data
    //     ];
    // }



    // public function inventoryCsvArray(array $inventoryArticleByLoca, $inventoryNumber)
    // {
    //     $timestamp = date('_H:i:s');

    //     $header = [
    //         'INVNO',  // Inventaire
    //         'INVDP',  // Dépôt
    //         'INVW1',  // Bordereau
    //         'INVID',  // Identifiant
    //         'INLIE',  // Description
    //         'INVAR',  // Article
    //         'HILOT',  // Lot
    //         'INVSN',  // Nombre
    //         'INVSC',  // Conditionnement
    //         'INVQS',  // QTE en US
    //         'INVSU',  // Unité
    //         'INVL1'   // 'N' = Génération d'inventaire à non.
    //     ];

    //     // Définition du chemin du fichier CSV
    //     // TODO: changer le nom du csv pour qu'il corresponde à RUBIS
    //     $filePath = $this->csvDirectoryInventory . "I_$inventoryNumber.csv";
    //     $fileName = basename($filePath);

    //     // Création du fichier CSV
    //     $file = fopen($filePath, 'w');

    //     if ($file === false) {
    //         throw new \Exception('Impossible de créer ou d\'ouvrir le fichier : ' . $filePath);
    //     }

    //     // Écriture de l'en-tête dans le fichier CSV
    //     fputcsv($file, $header, ';');

    //     // Boucle à travers chaque article dans l'inventaire
    //     foreach ($inventoryArticleByLoca as $inventoryArticle) {
    //         // Récupération des données de l'entité pour remplir chaque colonne
    //         $data = [
    //             $inventoryArticle->getInventoryNumber(),   // INVNO
    //             $inventoryArticle->getWarehouse(),         // INVDP
    //             $inventoryArticle->getLocation(),          // INVW1
    //             'INV_GEN',                                 // INVID
    //             'Inventaire test',                         // INLIE
    //             $inventoryArticle->getArticleCode(),       // INVAR
    //             $inventoryArticle->getLotCode(),           // HILOT
    //             $inventoryArticle->getQuantityLocation1(), // INVSN
    //             $inventoryArticle->getPackaging(),         // INVSC
    //             $inventoryArticle->getQuantityLocation1(), // INVQS
    //             $inventoryArticle->getPreparationUnit(),   // INVSU
    //             'N'                                        // INVL1
    //         ];

    //         // Écriture des données dans le fichier CSV
    //         fputcsv($file, $data, ';');
    //     }

    //     // Fermeture du fichier
    //     fclose($file);

    //     // Copie le CSV vers le QDLS
    //     $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryInventory, $fileName, $this->ERPDirProdINV);

    //     // Déplace le fichier CSV vers le dossier de sauvegarde (si nécessaire)
    //     //$this->moveCsvToSaveDirectory($fileName);

    //     return [
    //         'header' => $header,
    //         'fileName' => $fileName
    //     ];
    // }

    public function inventoryCsvArray(array $inventoryArticleByLoca, $inventoryNumber)
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
        $filePath = $this->csvDirectoryInventory . "I_$inventoryNumber.csv";
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
            // Liste des emplacements et quantités, même si elles sont nulles
            $locations = [
                ['location' => $inventoryArticle->getLocation(), 'quantity' => $inventoryArticle->getQuantityLocation1()],
                ['location' => $inventoryArticle->getLocation2(), 'quantity' => $inventoryArticle->getQuantityLocation2()],
                ['location' => $inventoryArticle->getLocation3(), 'quantity' => $inventoryArticle->getQuantityLocation3()],
            ];

            // Parcours de chaque emplacement (qu'il ait une quantité ou non)
            foreach ($locations as $loc) {
                // Création d'une ligne même si la quantité est nulle
                if ($loc['location']) { // Vérification que l'emplacement est défini
                    $data = [
                        $inventoryArticle->getInventoryNumber(),   // N° inventaire INVNO
                        $inventoryArticle->getWarehouse(),         // Dépôts INVDP
                        $loc['location'],                          // N° bordereau INVW1
                        'INV_GEN',                                 // Identifiant INVID
                        'Inventaire test',                         // N° de demande INLIE
                        $inventoryArticle->getArticleCode(),       // Code article INVAR
                        $inventoryArticle->getLotCode(),           // N° de lot HILOT
                        $loc['quantity'] !== null ? $loc['quantity'] : 0, // Nombre INVSN, mettre 0 si null mais utilité ?
                        $inventoryArticle->getPackaging(),         // Conditionnement INVSC
                        $loc['quantity'] !== null ? $loc['quantity'] : 0, // Quantité en unité de saisie INVQS, mettre 0 si null
                        $inventoryArticle->getPreparationUnit(),   // Unité de saisie INVSU
                        'N'                                        // Introuvable dans les ASAINVP1, paramètre de provenance ? INVL1
                    ];

                    // Écriture des données dans le fichier CSV
                    fputcsv($file, $data, ';');
                }
            }
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
