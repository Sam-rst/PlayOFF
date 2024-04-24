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
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
        $tournamentId = $tournament->getId();

        // Création des formulaires
        $addExistingPlayerForm = $this->createForm(AddExistingPlayerType::class);
        $createNewPlayerForm = $this->createForm(AddNewPlayerType::class);

        // Traitement de la soumission du formulaire pour l'ajout d'un joueur existant
        $addExistingPlayerForm->handleRequest($request);
        $createNewPlayerForm->handleRequest($request);

        // Initialiser la liste des joueurs pour ce tournoi
        $addedPlayers = $session->get('added_players', []);
        $tournamentPlayers = $addedPlayers[$tournamentId] ?? [];

        if ($addExistingPlayerForm->isSubmitted()) {
            $username = $addExistingPlayerForm->get('username')->getData();

            // Vérification si l'utilisateur existe dans la base de données
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->findOneBy(['username' => $username]);

            if ($user) {
                // Ajout de l'utilisateur à la liste des joueurs ajoutés en session
                if (!in_array($user->getUsername(), $addedPlayers)) {
                    $tournamentPlayers[] = $user->getUsername();
                    $addedPlayers[$tournamentId] = $tournamentPlayers;
                    $session->set('added_players', $addedPlayers);
                }
            } else {
                //
            }
        }


        // Traitement de la soumission du formulaire pour l'ajout d'un nouveau joueur
        if ($createNewPlayerForm->isSubmitted()) {
            $firstname = $createNewPlayerForm->get('firstname')->getData();
            $lastname = $createNewPlayerForm->get('lastname')->getData();
            $email = $createNewPlayerForm->get('email')->getData();

            // Vérification si l'email existe déjà dans la base de données
            $userRepository = $entityManager->getRepository(User::class);
            $existingUser = $userRepository->findOneBy(['email' => $email]);

            if ($existingUser) {
                $this->addFlash('warning', 'Un utilisateur avec cet email existe déjà.');
            } else {
                // Création d'un nouvel utilisateur
                $player = new User();
                $player->setFirstname($firstname);
                $player->setLastname($lastname);
                $player->setEmail($email);
                $player->setPassword('');
                $player->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $player->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

                $entityManager->persist($player);
                $entityManager->flush();

                // Ajout de l'utilisateur à la liste des joueurs ajoutés en session
                $newPlayerName = $firstname . ' ' . $lastname;
                if (!in_array($newPlayerName, $tournamentPlayers)) {
                    $tournamentPlayers[] = $newPlayerName;
                    $addedPlayers[$tournamentId] = $tournamentPlayers;
                    $session->set('added_players', $addedPlayers);
                }
            }
        }

        if ($request->request->get('validate')) {
            // Logique pour clôturer les équipes 
            // ...

            // Redirection vers la page du tournoi
            return $this->redirectToRoute('tournament_show', ['id' => $tournament->getId()]);
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

    #[Route('/tournament/remove_player/{username}', name: 'tournament_remove_player', methods: ['POST'])]
    public function removePlayer(Request $request, SessionInterface $session, $username) {
        // Logique pour retirer un joueur de la session ou de la base de données
        // Répondre avec un JSON
        return new JsonResponse(['success' => true]);
    }
    
    #[Route('/tournament/swap_players', name: 'tournament_swap_players', methods: ['POST'])]
    public function swapPlayers(Request $request, SessionInterface $session) {
        // Logique pour échanger les joueurs
        // Répondre avec un JSON
        return new JsonResponse(['success' => true]);
    }
}
