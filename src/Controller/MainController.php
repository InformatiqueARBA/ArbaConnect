<?php

namespace App\Controller;

use App\Entity\Security\ACComment;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Security $security, ManagerRegistry $managerRegistry, Request $request): Response
    {
        //affichage commentaire sur toutes les pages
        $em = $managerRegistry->getManager('security');
        $AAComment = $em->getRepository(ACComment::class)->find(1);
        if ($AAComment !== null) {
            $comment = $AAComment->getComment();
            $session = $request->getSession();
            $session->set('comment', $comment);
        }


        $user = $security->getUser();


        if ($user && in_array('ROLE_ARBA', $user->getRoles())) {
            return $this->redirectToRoute('app_dates_livraisons');
        } else {
            return $this->redirectToRoute('app_dates_livraisons_adherent');
        }
    }
}
