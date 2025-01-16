<?php

namespace App\ArbaConnect\Controller;

use App\ArbaConnect\Form\ChangePasswordType;
use App\ArbaConnect\Form\ForgottenPasswordType;
use App\ArbaConnect\Service\JWTService;
use App\ArbaConnect\Service\MailerService;
use App\Entity\Security\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/*
    Gestion des mots de passe utilisateur
*/

class PasswordController extends AbstractController
{
    // Gère les demande de changement de mot de passe : Avec le login renseigné, envoi un mail d'accès avec token à la page de changement password
    #[Route(path: '/forgotten-password', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $request, ManagerRegistry $managerRegistry, SessionInterface $session, MailerService $mailer, JWTService $jwtService, ParameterBagInterface $params): Response
    {
        $em = $managerRegistry->getManager('security');
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $login = $data['login'];

            $user = $em->getRepository(User::class)->findUserByLogin($login);

            if ($user) {
                // Récupération de l'adresse e-mail AR en fonction de l'environnement
                if ($this->getParameter('kernel.environment') === 'dev') {
                    $email = $params->get('mail_pwd_dev');
                } else {
                    $email = $user->getMail();
                }

                $session->set('emailSent', true);
                $session->set('email', $email);

                // Générer un token JWT
                $token = $jwtService->generateToken(['user_id' => $user->getLogin()]);

                // Créer le lien de réinitialisation avec le token
                $resetUrl = $this->generateUrl('app_change_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                //Génération du mail & injection du lien sécurisé
                $subject = 'ARBA | Changement de mot de passe';
                $content =  "Bonjour, <br><br> Voici le lien pour réinitialiser votre mot de passe : <br><br> <a href=$resetUrl> ArbaConnect</a>";

                //Expédition du mail
                $mailer->sendMail($email, $subject, $content);
                $this->addFlash('success', "Un email a été envoyé à l'adresse: $email");
            } else {
                $session->set('emailSent', false);
                $session->set('email', '');
                $this->addFlash('warning', 'Utilisateur non trouvé.');
            }
            //session_unset();
            return $this->redirectToRoute('app_home');
        }

        //Initialise les variables à faux pour que la vue affiche le formulaire au 1er passage
        // $emailSent = $session->get('emailSent', false);
        $email = $session->get('email', '');

        return $this->render('ArbaConnect/password/loginForgottenPass.html.twig', [
            'form' => $form->createView(),
            // 'emailSent' => $emailSent,
            'email' => $email,
        ]);
    }


    // Récupère le nouveau mot de passe pour mise à jour en DB
    #[Route(path: '/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $hasher, JWTService $jwtService, SessionInterface $session)
    {
        $em = $managerRegistry->getManager('security');
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        // on sette emailSent à false car cette variable sert à modifier l'affichage
        // dans  la vue loginForgottenPass.html.twig (si ce n'est pas fait l'utilisateur ne peut pas appuyer une seconde fois sur  mot de passe oublié)
        // $session->set('emailSent', false);

        //TODO: imposer un mot de passe différent du précédant et definir pattern à respecter
        if ($form->isSubmitted() && $form->isValid()) {

            // Extraction du password
            $data = $form->getData();
            $password = $data['new_password'];

            //Extraction du token sécurisé
            $token = $request->query->get('token');

            //Déclanche le process si token existant
            if (!$token) {
                $this->addFlash('warning', 'Token manquant.');
                return $this->redirectToRoute('app_home');
            }
            //Contrôle la validité du délai & de l'origine de la demande
            try {
                $claims = $jwtService->validateToken($token);
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Token non valide ou expiré.');
                return $this->redirectToRoute('app_home');
            }
            //Récupère l'ID de l'utilisateur
            $userId = $claims['data']['user_id'];
            $user = $em->getRepository(User::class)->findUserByLogin($userId);

            //MaJ du password
            if ($user) {
                $user->setPassword($hasher->hashPassword($user, $password));
                $em->flush();

                $this->addFlash('success', "Votre mot de passe a été changé");
            } else {

                $this->addFlash('warning', 'Utilisateur non trouvé.');
            }

            return $this->redirectToRoute('app_home');
        }
        return $this->render('ArbaConnect/password/changePassword.html.twig', [
            'form' => $form
        ]);
    }
}
