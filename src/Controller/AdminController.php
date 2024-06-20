<?php

namespace App\Controller;

use App\Entity\Security\User;
use App\Form\PasswordType;
use App\Form\UserType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $hasher): Response
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

        return $this->render('admin/index.html.twig', [
            'form' => $form //Attenton on a viré le Createview()
        ]);
    }

    #[Route('/mail', name: 'app_mail')]
    public function mail(MailerService $mailer)
    {
        $to = 'boitedetestsam@gmail.com';
        $subject = 'ARBA | We did it !';
        $content = 'Hello World';


        $mailer->sendMail($to, $subject, $content);

        return new Response('Juan was here :)');
    }
}
