<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        if ($user && in_array('ROLE_ARBA', $user->getRoles())) {
            //dd($user);
            return $this->redirectToRoute('app_dates_livraisons');
            //('L\'utilisateur est connecté en tant que ROLE_ARBA.');
        } else {
            return $this->redirectToRoute('app_dates_livraisons_adherent');
            //('L\'utilisateur n\'est pas connecté en tant que ROLE_ARBA.');
        }
    }
}
