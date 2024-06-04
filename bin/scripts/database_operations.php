<?php
// bin/scripts/database_operations.php

use App\Service\DataMapperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;

// Charger l'autoload de Composer
require_once __DIR__ . '/../../vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../../.env.local');

// Charger le kernel Symfony
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

// Récupérer le conteneur de services
$container = $kernel->getContainer();

// Récupérer les services
$entityManager = $container->get(EntityManagerInterface::class);
$dataMapperService = $container->get(DataMapperService::class);

// Get the connection from the entity manager
$connection = $entityManager->getConnection();

try {
    // Start the transaction
    $connection->beginTransaction();

    // Truncate the user table
    $connection->executeStatement('DELETE FROM user');

    // Truncate the order table
    $connection->executeStatement('DELETE FROM `order`');

    // Truncate the corporation table
    $connection->executeStatement('DELETE FROM corporation');

    // Commit the transaction if all statements are successful
    $connection->commit();
} catch (\Exception $e) {
    // Rollback the transaction in case of error
    $connection->rollBack();
    throw $e;
}

$dataMapperService->corporationMapper();
$dataMapperService->orderMapper();
$dataMapperService->userMapper();
