<?php

namespace App\Service;

use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DataMapperSecurityService
{

    private $requestOdbcService;
    private $odbcService;
    private $em;

    public function __construct(RequestOdbcService $requestOdbcService, OdbcService $odbcService, ManagerRegistry $doctrine)
    {
        $this->requestOdbcService = $requestOdbcService;
        $this->odbcService = $odbcService;
        $this->em = $doctrine->getManager('security');
    }



    public function userMapper(UserPasswordHasherInterface $hasher): void
    {
        $sql = $this->requestOdbcService->getusers();
        $results = $this->odbcService->executeQuery($sql);

        foreach ($results as $result) {

            $existingUser = $this->em->getRepository(User::class)->findOneBy(['login' => $result['LOGIN']]);

            if (!$existingUser) {
                // Create a new user if not exists
                $user = new User();
                $user->setLogin($result['LOGIN']);
                $user->setMail($result['MAIL']);
                $user->setEnterprise($result['ENTERPRISE']);
                $user->setRoles([]);
                $user->setPassword($hasher->hashPassword($user, '0000'));
    
                $this->em->persist($user);
            } else {
                // Update existing user's details if needed
                $existingUser->setMail($result['MAIL']);
                $existingUser->setEnterprise($result['ENTERPRISE']);
                $existingUser->setPassword($hasher->hashPassword($existingUser, '0000'));
            }

        $this->em->flush();
    }


}
}