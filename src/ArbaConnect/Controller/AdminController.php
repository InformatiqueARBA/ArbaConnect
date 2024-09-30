<?php

namespace App\ArbaConnect\Controller;

use App\ArbaConnect\Form\ACCommentType;
use App\ArbaConnect\Form\UserType;
use App\Entity\Security\ACComment;
use App\Entity\Security\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $hasher): Response
    {
        return $this->render('ArbaConnect/admin/admin.html.twig', []);
    }



    #[Route('/admin/creation/user', name: 'admin_create_user')]
    public function createUser(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $hasher): Response
    {
        $em = $managerRegistry->getManager('security');
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Les données du formulaire sont déjà mappées dans l'objet $user
            $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            // Optionnel: ajout d'un message flash ou redirection
            $this->addFlash('success', 'L\'utilisateur a été créé avec succès.');

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('ArbaConnect/admin/CreateUser.html.twig', [
            'form' => $form,
        ]);
    }




    #[Route('/admin/commentaire', name: 'admin_commentary')]
    public function commentary(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $hasher): Response
    {
        $em = $managerRegistry->getManager('security');
        $ACComment = new ACComment();
        $formACComment = $this->createForm(ACCommentType::class);
        $formACComment->handleRequest($request);

        if ($formACComment->isSubmitted() && $formACComment->isValid()) {
            // Clear the ACComment table and reset IDs
            $this->truncateACCommentTable($em);

            $ACComment->setComment($formACComment->get('comment')->getData());
            $em->persist($ACComment);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a été soumis avec succès.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('ArbaConnect/admin/commentary.html.twig', [
            'formACComment' =>  $formACComment
        ]);
    }


    // fonction permettant de vider la table commentaire de la DB Security
    private function truncateACCommentTable($em)
    {
        $connection = $em->getConnection();
        $platform = $connection->getDatabasePlatform();
        // Truncate la table et reset le compteur id
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement($platform->getTruncateTableSQL('ACComment', true /* si en cascade */));
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }




    #[Route('/admin/inventaire', name: 'admin_inventory')]
    public function inventory(): Response
    {


        return $this->render('ArbaConnect/admin/inventory.html.twig', []);
    }
}
