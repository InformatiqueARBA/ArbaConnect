<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DatabaseSwitcherService
{


    private $defaultEntityManager;
    private $customerEntityManager;
    private $variableDataSwitcher;





    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $defaultEntityManager, EntityManagerInterface $customerEntityManager, ParameterBagInterface $params)
    {
        $this->defaultEntityManager = $doctrine->getManager('default');
        $this->customerEntityManager = $doctrine->getManager('customer');
        $this->variableDataSwitcher = $params->get('variables_app_directory');
    }







    public function getEntityManager(): EntityManagerInterface
    {

        // Construire le chemin complet vers le fichier variableDataSwitcher.txt
        $filePath = $this->variableDataSwitcher . DIRECTORY_SEPARATOR . 'variableDataSwitcher.txt';

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            throw new \Exception("Le fichier $filePath n'existe pas.");
        };
        $boolDB = boolval(file_get_contents($filePath));

        return $boolDB ? $this->defaultEntityManager : $this->customerEntityManager;
    }
}
