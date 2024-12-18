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
        $this->csvDirectoryInventory = $params->get('csv_directory_inventory_inventory_sheets');
        $this->csvSaveDirectoryInventory = $params->get('csv_save_directory_inventory');
        $this->csvToRubisService = $csvToRubisService;
        $this->ERPDirProdINV = $params->get('erp_dir_prod_inv');
    }




    public function inventoryCsvArray(array $inventoryArticleByLoca, $inventoryNumber)
    {
        $timestamp = date('_H:i:s');
        $unitTabs = ['M2', 'M3', 'ML', 'PCES',];

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
            'INVQS',  // QTE saisie
            'INVSU',  // Unité de saisie
            'INVL1'   // 'N' = Génération d'inventaire à non. ATPDINP1

        ];

        // Définition du chemin du fichier CSV
        $filePath = $this->csvDirectoryInventory . "stock/I_$inventoryNumber.csv";
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

            if ($inventoryArticle->getTypeArticle() != 'LOV') {
                $data = [
                    $inventoryArticle->getInventoryNumber(),   // N° inventaire INVNO
                    $inventoryArticle->getWarehouse(),         // Dépôts INVDP
                    $inventoryArticle->getLocation(),          // N° bordereau INVW1
                    'INV_GEN',                                 // Identifiant INVID
                    'Inventaire stock',                        // N° de demande INLIE  ATPDINP1
                    $inventoryArticle->getArticleCode(),       // Code article INVAR
                    $inventoryArticle->getLotCode(),           // N° de lot HILOT

                    // Nombre ATPDINP1
                    $INVSN = in_array($inventoryArticle->getPreparationUnit(), $unitTabs) ||
                        ($inventoryArticle->getPreparationUnit() === 'UN' &&
                            ($inventoryArticle->getPackaging() === '' || $inventoryArticle->getPackaging() === null) &&
                            $inventoryArticle->isDivisible() === false)
                        ? $inventoryArticle->getQuantityLocation1() : 0,

                    // Conditionnement ATPDINP1
                    $INVSC = in_array($inventoryArticle->getPreparationUnit(), $unitTabs) ||
                        ($inventoryArticle->getPreparationUnit() === 'UN' &&
                            ($inventoryArticle->getPackaging() === '' || $inventoryArticle->getPackaging() === null) &&
                            $inventoryArticle->isDivisible() === false)
                        ? $inventoryArticle->getPackaging()
                        : 0,
                    // Unité de saisie
                    $INVSU = in_array($inventoryArticle->getPreparationUnit(), $unitTabs) ||
                        ($inventoryArticle->getPreparationUnit() === 'UN' &&
                            ($inventoryArticle->getPackaging() === '' || $inventoryArticle->getPackaging() === null) &&
                            $inventoryArticle->isDivisible() === false)
                        ? $inventoryArticle->getQuantityLocation1() * $inventoryArticle->getPackaging()
                        : $inventoryArticle->getQuantityLocation1(),

                    $inventoryArticle->getPreparationUnit(),
                    'N'

                ];

                fputcsv($file, $data, ';');
            }
        }

        // Fermeture du fichier
        fclose($file);

        // Copie le CSV vers le QDLS
        $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryInventory . "stock/", $fileName, $this->ERPDirProdINV);
    }




    public function inventoryLotCsvArray(array $inventoryArticleByLoca, $inventoryNumber)
    {

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
            'INVQS',  // QTE saisie
            'INVSU',  // Unité de saisie
            'INVL1'   // 'N' = Génération d'inventaire à non. ATPDINP1

        ];

        // Définition du chemin du fichier CSV
        $filePath = $this->csvDirectoryInventory . "lot/IL$inventoryNumber.csv";
        $fileName = basename($filePath);

        // Création du fichier CSV
        $file = fopen($filePath, 'w');

        if ($file === false) {
            throw new \Exception('Impossible de créer ou d\'ouvrir le fichier : ' . $filePath);
        }

        // Écriture de l'en-tête dans le fichier CSV
        fputcsv($file, $header, ';');

        foreach ($inventoryArticleByLoca as $inventoryArticle) {
            if ($inventoryArticle->getTypeArticle() == 'LOV') {
                $data = [
                    $inventoryArticle->getInventoryNumber(),   // N° inventaire INSNO
                    $inventoryArticle->getWarehouse(),         // Dépôts INSDP
                    $inventoryArticle->getLocation(), // N° bordereau INSBO
                    'INV_LOT', // Identifiant
                    'Inventaire lot', // Description ATPDINP1
                    $inventoryArticle->getArticleCode(),  // Code article INSAR
                    $inventoryArticle->getLotCode(),  // N° de lot INSLO
                    $inventoryArticle->getQuantityLocation1(), // Nombre ATPDINP1 
                    $inventoryArticle->getPackaging(), // Conditionnement ATPDINP1
                    $inventoryArticle->getQuantityLocation1() !== null ?
                        $inventoryArticle->getQuantityLocation1() : 0,  // Quantité en unité de saisie INSQS, mettre 0 si null
                    $inventoryArticle->getPreparationUnit(),  // Unité de saisie
                    'N' // 'N' = Génération d'inventaire à non. ATPDINP1

                ];
                fputcsv($file, $data, ';');
            }
        }



        // Copie le CSV vers le QDLS
        $this->csvToRubisService->sendCsvToRubis($this->csvDirectoryInventory . "lot/", $fileName, $this->ERPDirProdINV);
    }
}
