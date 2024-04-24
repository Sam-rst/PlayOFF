<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(UserRepository $userRepository): Response
    {
        $user = $userRepository->find(100);   
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }

    #[Route('/profile/my_tournaments', name: 'my_tournaments')]
    public function myTournaments(): Response
    {
        return $this->render('tournament/my_tournaments.html.twig');
    }
}
