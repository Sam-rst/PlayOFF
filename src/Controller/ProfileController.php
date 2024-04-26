<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_register');
        }
    
        // Récupérer les tournois auxquels l'utilisateur a participé
        $tournamentsParticipated = $user->getTournamentsParticipated();
    

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'tournamentsParticipated' => $tournamentsParticipated,
        ]);
    }

    #[Route('/profile/my_tournaments', name: 'my_tournaments')]
    public function myTournaments(): Response
    {
        return $this->render('tournament/my_tournaments.html.twig');
    }
}
 