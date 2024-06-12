<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class DatabaseSwitcherService
{


    private $defaultEntityManager;
    private $customerEntityManager;
    private $variableDataSwitcherPath;





    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $defaultEntityManager, EntityManagerInterface $customerEntityManager, ParameterBagInterface $params)
    {
        $this->defaultEntityManager = $doctrine->getManager('default');
        $this->customerEntityManager = $doctrine->getManager('customer');
        $this->variableDataSwitcherPath = $params->get('variables_app_directory') . DIRECTORY_SEPARATOR . 'variableDataSwitcher.txt';
    }







    // fonction retournant le manager utiliser pour l'affichage dans l'ihm (en fonction de la valeur dans le fichier varibleDataSwitcher.txt => 'default' si 1 'customer' si 0)
    public function getEntityManager(): EntityManagerInterface
    {
        $boolDB = $this->getDatabaseSwitchValue();
        return $boolDB ? $this->defaultEntityManager : $this->customerEntityManager;
    }






    // fonction retournant le manager utiliser pour la mise à jour des BDD (en fonction de la valeur dans le fichier varibleDataSwitcher.txt => 'default' si 0 'customer' si 1)
    // On met à jour la BDD qui n'est pas affichée dans l'ihm
    public function getEntityManagerPopulate(): EntityManagerInterface
    {
        $boolDB = $this->getDatabaseSwitchValue();
        return $boolDB ? $this->customerEntityManager : $this->defaultEntityManager;
    }






    // lis et retourne la valeur de switch contenu dans le fichier 'variableDataSwitcher.txt'
    private function getDatabaseSwitchValue(): bool
    {
        if (!file_exists($this->variableDataSwitcherPath)) {
            throw new FileNotFoundException("The file {$this->variableDataSwitcherPath} does not exist.");
        }

        return filter_var(file_get_contents($this->variableDataSwitcherPath), FILTER_VALIDATE_BOOLEAN);
    }
}
