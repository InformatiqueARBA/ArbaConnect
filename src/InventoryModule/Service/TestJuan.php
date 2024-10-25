<?php

require_once './PrinterServiceCOUCOU.php'; // Remplacez par le chemin correct vers PrinterService.php

use App\InventoryModule\Service\PrinterServiceCOUCOU;

// Créer une instance de PrinterService
$printerService = new PrinterServiceCOUCOU();

// Appeler la fonction pour tester
$printerService->PDFPrinter('Menuiserie'); // Remplacez 'Menuiserie' par le nom de l'imprimante souhaité
