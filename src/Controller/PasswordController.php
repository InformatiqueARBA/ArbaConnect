<?php

namespace App\Controller;

use App\Entity\Security\User;
use App\Form\ChangePasswordType;
use App\Form\ForgottenPasswordType;
use App\Service\JWTService;
use App\Service\MailerService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordController extends AbstractController
{
    #[Route(path: '/forgotten-password', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $request, ManagerRegistry $managerRegistry, SessionInterface $session, MailerService $mailer, JWTService $jwtService): Response
    {
        $em = $managerRegistry->getManager('security');
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $login = $data['login'];

            $user = $em->getRepository(User::class)->findUserByLogin($login);

            if ($user) {
                $email = 'boitedetestsam@gmail.com'; //$email = $user->getMail(); TODO: À réactiver quand tout est OK.
                $session->set('emailSent', true);
                $session->set('email', $email);

                // Générer un token JWT
                $token = $jwtService->generateToken(['user_id' => $user->getLogin()]);
                //dd($token);

                // Créer le lien de réinitialisation avec le token
                $resetUrl = $this->generateUrl('app_change_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $email = 'boitedetestsam@gmail.com';
                $subject = 'ARBA | Changement de mot de passe';
                $content =  "Bonjour, <br><br> Voici le lien pour réinitialiser votre mot de passe : <br> <a href=$resetUrl> ARBA.COOP</a>";
                $mailer->sendMail($email, $subject, $content);


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

        return $this->render('password/loginForgottenPass.html.twig', [
            'form' => $form->createView(),
            'emailSent' => $emailSent,
            'email' => $email,
        ]);
    }


    #[Route(path: '/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $hasher, JWTService $jwtService)
    {
        $em = $managerRegistry->getManager('security');
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        $login = '016016';

        //TODO: imposer un mot de passe différent du précédant et definir pattern à respecter
        if ($form->isSubmitted() && $form->isValid()) {


            $data = $form->getData();
            $password = $data['new_password'];

            $token = $request->query->get('token');

            if (!$token) {
                $this->addFlash('warning', 'Token manquant.');
                return $this->redirectToRoute('app_home');
            }
            //dd($token);
            try {
                $claims = $jwtService->validateToken($token);
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Token non valide ou expiré.');
                return $this->redirectToRoute('app_home');
            }

            $userId = $claims['data']['user_id'];
            //dd($userId);
            $user = $em->getRepository(User::class)->findUserByLogin($userId);

            if ($user) {
                $user->setPassword($hasher->hashPassword($user, $password));
                $em->flush();

                $this->addFlash('success', "Votre mot de pass a été changé");
            } else {

                $this->addFlash('warning', 'Utilisateur non trouvé.');
            }

            return $this->redirectToRoute('app_home');
        }
        return $this->render('password/changePassword.html.twig', [
            'form' => $form,
            'login' => $login
        ]);
    }
}
