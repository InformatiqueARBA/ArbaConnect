<?php

namespace App\ArbaConnect\Service;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/*
Service qui créé, màj ou supprime les utilisateurs 
utilse UserPasswordHasherInterface pour hasher les mdp 
*/

class DataMapperSecurityService
{

    private $requestOdbcArbaConnectService;
    private $odbcService;
    private $em;
    private $managerRegistry;
    private $hasher;

    public function __construct(RequestOdbcArbaConnectService $requestOdbcArbaConnectService, OdbcService $odbcService, ManagerRegistry $managerRegistry, EntityManagerInterface $securityEntityManager, UserPasswordHasherInterface $hasher)
    {
        $this->requestOdbcArbaConnectService = $requestOdbcArbaConnectService;
        $this->odbcService = $odbcService;
        $this->managerRegistry = $managerRegistry;
        $this->em = $managerRegistry->getManager('security');
        $this->hasher = $hasher;
    }



    public function userMapper(): void
    {
        $batchSize = 20;
        $i = 0;

        // augmente le temps d'exécution à 5 minutes (initialement à 30 secondes)
        ini_set('max_execution_time', 300);

        // récupère et exécute la requête getUsers --- création de la connexion 
        $sql = $this->requestOdbcArbaConnectService->getusers();
        $results = $this->odbcService->executeQuery($sql);
        $connection = $this->managerRegistry->getConnection();

        $connection->beginTransaction();

        try {
            foreach ($results as $result) {
                // récupère les User de la DB SECURITY
                $existingUser = $this->em->getRepository(User::class)->findOneBy(['login' => $result['LOGIN']]);

                // si nouvel utilisateur et status non supendu insertion en base
                if (!$existingUser && $result['STATUS'] != 'S') {
                    $user = new User();
                    $user->setLogin($result['LOGIN']);
                    $user->setMail($result['MAIL']);
                    $user->setMailAR($result['MAIL_AR']);
                    $user->setEnterprise($result['ENTERPRISE']);
                    $user->setRoles([]);
                    $user->setStatus($result['STATUS']);
                    $user->setTourCode($result['TOUR_CODE']);
                    $user->setPassword($this->hasher->hashPassword($user, '0000'));

                    $this->em->persist($user);
                    // SI changement d'une donnée courante dans Rubis, MaJ sur l'appli
                } elseif (
                    $existingUser && $result['STATUS'] != 'S'
                    &&  ($result['MAIL'] != $existingUser->getMail()
                        || $result['MAIL_AR'] != $existingUser->getMailAR()
                        || $result['ENTERPRISE'] != $existingUser->getEnterprise()
                        || $result['TOUR_CODE'] != $existingUser->getTourCode())
                ) {
                    $existingUser->setMail($result['MAIL']);
                    $existingUser->setMailAR($result['MAIL_AR']);
                    $existingUser->setEnterprise($result['ENTERPRISE']);
                    $existingUser->setTourCode($result['TOUR_CODE']);

                    $this->em->persist($existingUser);
                    // si suspendu Rubis suppression de l'utilisateur
                } elseif ($existingUser && $result['STATUS'] == 'S') {
                    $this->em->remove($existingUser);
                }

                // optimisation : flush tous les 20 adhérents
                if (($i % $batchSize) === 0) {
                    $this->em->flush();
                    $this->em->clear(); // Détache tous les objets de Doctrine pour des raisons de performance
                }
                $i++;
            }

            // Effectuer le flush pour les objets restants
            $this->em->flush();
            $this->em->clear();

            // Valider la transaction
            $connection->commit();
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            //TODO: créé une exception personnalisé
            $connection->rollback();
            throw $e;
        }
    }
}
