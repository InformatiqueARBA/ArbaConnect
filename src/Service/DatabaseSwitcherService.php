<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class DatabaseSwitcherService
{


    private $defaultEntityManager;
    private $customerEntityManager;




    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $defaultEntityManager, EntityManagerInterface $customerEntityManager)
    {
        $this->defaultEntityManager = $doctrine->getManager('default');
        $this->customerEntityManager = $doctrine->getManager('customer');
    }







    public function getEntityManager(bool $boolDB): EntityManagerInterface
    {
        $defaultRepo = $this->defaultEntityManager;
        $customerRepo = $this->customerEntityManager;

        return $boolDB ? $defaultRepo : $customerRepo;
    }
}
