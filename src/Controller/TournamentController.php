<?php

// src/Controller/TournamentController.php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\User;
use App\Form\AddExistingPlayerType;
use App\Form\AddNewPlayerType;
use App\Form\TournamentType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TournamentController extends AbstractController
{
    #[Route('/tournament', name: 'app_tournament')]
    public function index(): Response
    {
        return $this->render('tournament/index.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }

    #[Route('/tournament/new', name: 'tournament_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tournament = new Tournament();
        $form = $this->createForm(TournamentType::class, $tournament);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $tournament->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

            $entityManager->persist($tournament);
            $entityManager->flush();

            $this->addFlash('success', 'Tournament created successfully!');
            return $this->redirectToRoute('tournament_add_players', ['id' => $tournament->getId()]);
        }

        // If the form is not submitted or not valid, render the form

        $this->addFlash('error', 'Errors Submit Form');

        return $this->render('tournament/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tournament/success', name: 'tournament_success')]
    public function success(): Response
    {
        return $this->render('tournament/succes.html.twig');
    }

    #[Route('/tournament/{id}/add_players', name: 'tournament_add_players')]
    public function addPlayers(Tournament $tournament, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Création des formulaires
        $addExistingPlayerForm = $this->createForm(AddExistingPlayerType::class);
        $createNewPlayerForm = $this->createForm(AddNewPlayerType::class);

        // Traitement de la soumission du formulaire
        $addExistingPlayerForm->handleRequest($request);

        if ($addExistingPlayerForm->isSubmitted()) {
            $username = $addExistingPlayerForm->get('username')->getData();

            // Vérification si l'utilisateur existe dans la base de données
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

            if ($user) {
                // Ajout de l'utilisateur à la liste des joueurs ajoutés en session
                $addedPlayers = $session->get('added_players', []);
                $addedPlayers[] = $user->getUsername();
                $session->set('added_players', $addedPlayers);
            } else {
                // Gestion de l'erreur si l'utilisateur n'est pas trouvé

            }
        }


        $createNewPlayerForm->handleRequest($request);

        if ($createNewPlayerForm->isSubmitted()) {
            $player = new User();
            $player->setEmail($createNewPlayerForm->get('email')->getData());

            $entityManager->persist($player);
            $entityManager->flush();

            // Logique pour envoyer l'email ou créer le lien d'invitation

            // Redirigez ou affichez un message de succès
        }

        $addedPlayers = $session->get('added_players', []);

        return $this->render('tournament/add_players.html.twig', [
            'tournament' => $tournament,
            'add_existing_player_form' => $addExistingPlayerForm->createView(),
            'create_new_player_form' => $createNewPlayerForm->createView(),
            'added_players' => $addedPlayers,
        ]);
    }


    #[Route('/search/users', name: 'ajax_search_users')]
    public function ajaxSearchUsers(Request $request, UserRepository $userRepository): JsonResponse
    {
        $searchTerm = $request->query->get('term', '');
        $users = $userRepository->findByUsernameLike($searchTerm);
    
        $usernames = array_map(function (User $user) {
            return $user->getUsername();
        }, $users);
    
        return new JsonResponse($usernames);
    }
}
