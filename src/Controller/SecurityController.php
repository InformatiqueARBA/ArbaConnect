<?php

namespace App\Controller;

use App\Entity\Security\User;
use App\Form\ChangePasswordType;
use App\Form\ForgottenPasswordType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgotten-password', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $request, ManagerRegistry $managerRegistry, SessionInterface $session): Response
    {
        $em = $managerRegistry->getManager('security');
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $login = $data['login'];

            $user = $em->getRepository(User::class)->findUserByLogin($login);

            if ($user) {
                $email = $user->getMail();
                $session->set('emailSent', true);
                $session->set('email', $email);
                $this->addFlash('success', "Un email a été envoyé à l'adresse: $email");
            } else {
                $session->set('emailSent', false);
                $session->set('email', '');
                $this->addFlash('warning', 'Utilisateur non trouvé.');
            }

            return $this->redirectToRoute('app_forgotten_password');
        }

        $emailSent = $session->get('emailSent', false);
        $email = $session->get('email', '');

        return $this->render('security/loginForgottenPass.html.twig', [
            'form' => $form->createView(),
            'emailSent' => $emailSent,
            'email' => $email,
        ]);
    }


    #[Route(path: '/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $hasher)
    {
        $em = $managerRegistry->getManager('security');
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        $login = '016016';

        //TODO: imposer un mot de passe différent du précédant et definir pattern à respecter
        if ($form->isSubmitted() && $form->isValid()) {


            $data = $form->getData();
            $password = $data['new_password'];


            $user = $em->getRepository(User::class)->findUserByLogin($login);

            if ($user) {
                $user->setPassword($hasher->hashPassword($user, $password));
                $em->flush();

                $this->addFlash('success', "Votre mot de pass a été changé");
            } else {

                $this->addFlash('warning', 'Utilisateur non trouvé.');
            }

            return $this->redirectToRoute('app_home');
        }
        return $this->render('security/changePassword.html.twig', [
            'form' => $form,
            'login' => $login
        ]);
    }
}
