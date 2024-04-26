<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TournamentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(TournamentRepository $tournamentRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_register');
        }

        // Récupérer les tournois organisés par l'utilisateur
        $organizerTournaments = $tournamentRepository->findBy(['organisator' => $user]);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'organizerTournaments' => $organizerTournaments,
        ]);
    }

    #[Route('/profile/my_tournaments', name: 'my_tournaments')]
    public function myTournaments(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('Utilisateur non trouvé.');
        }

       

        $tournamentsParticipated = $user->getTournamentsParticipated();

        return $this->render('profile/my_tournaments.html.twig', [
            'user' => $user,
            'tournamentsParticipated' => $tournamentsParticipated,
        ]);
    
    }
    
}

