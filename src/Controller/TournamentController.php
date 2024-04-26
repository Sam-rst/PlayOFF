<?php

// src/Controller/TournamentController.php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\User;
use App\Form\AddExistingPlayerType;
use App\Form\AddNewPlayerType;
use App\Form\TournamentType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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

    #[Route('/tournament/{id}/open', name: 'tournament_open')]
    public function openTournament(Tournament $tournament): Response
    {
        switch ($tournament->getStatus()) {
            case Tournament::STATUS_INITIALIZATION:
            case Tournament::STATUS_ADD_PLAYERS:
                return $this->redirectToRoute('tournament_add_players', ['id' => $tournament->getId()]);
            case Tournament::STATUS_SELECT_MATCHES:
                return $this->redirectToRoute('tournament_select_matches', ['id' => $tournament->getId()]);
            case Tournament::STATUS_IN_PROGRESS:
                return $this->redirectToRoute('tournament_show', ['id' => $tournament->getId()]);
            case Tournament::STATUS_FINISHED:
                return $this->redirectToRoute('tournament_finished', ['id' => $tournament->getId()]);
            default:
                return $this->redirectToRoute('home');
        }
    }

    #[Route('/tournament/new', name: 'tournament_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $tournament = new Tournament();
        $form = $this->createForm(TournamentType::class, $tournament);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $organisator = $security->getUser();

            $tournament->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $tournament->setOrganisator($organisator);
            
            $entityManager->persist($tournament);
            $entityManager->flush();

            $this->addFlash('success', 'Tournament created successfully!');
            $tournament->setStatus(Tournament::STATUS_ADD_PLAYERS);
            return $this->redirectToRoute('tournament_add_players', ['id' => $tournament->getId()]);
        }

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

        $sessionKey = 'added_players_' . $tournamentId;

        // Initialiser la liste des joueurs pour ce tournoi
        $addedPlayers = $session->get($sessionKey, []);
        $tournamentPlayers = $addedPlayers ?? [];

        if ($addExistingPlayerForm->isSubmitted()) {
            $username = $addExistingPlayerForm->get('username')->getData();

            // Vérification si l'utilisateur existe dans la base de données
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->findOneBy(['username' => $username]);

            if ($user) {
                // Ajout de l'utilisateur à la liste des joueurs ajoutés en session
                if (!in_array($user->getUsername(), $tournamentPlayers)) {
                    $tournamentPlayers[] = $user->getUsername();
                    $session->set($sessionKey, $tournamentPlayers);
                }
            } else {
                //
            }
        }

        // Traitement de la soumission du formulaire pour l'ajout d'un nouveau joueur
        if ($createNewPlayerForm->isSubmitted()) {
            $username = $createNewPlayerForm->get('username')->getData();
            $firstname = $createNewPlayerForm->get('firstname')->getData();
            $lastname = $createNewPlayerForm->get('lastname')->getData();

            // Vérification si l'email existe déjà dans la base de données
            $userRepository = $entityManager->getRepository(User::class);
            $existingUser = $userRepository->findOneBy(['username' => $username]);

            if ($existingUser) {
                $this->addFlash('warning', 'ce pseudo est deja utiliser.');
            } else {
                // Création d'un nouvel utilisateur
                $player = new User();
                $player->setUsername($username);
                $player->setFirstname($firstname);
                $player->setLastname($lastname);
                $player->setPassword('');
                $player->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $player->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

                $entityManager->persist($player);
                $entityManager->flush();

                if (!in_array($username, $tournamentPlayers)) {
                    $tournamentPlayers[] = $username;
                    $session->set($sessionKey, $tournamentPlayers);
                }
            }
        }

        return $this->render('tournament/add_players.html.twig', [
            'tournament' => $tournament,
            'add_existing_player_form' => $addExistingPlayerForm->createView(),
            'create_new_player_form' => $createNewPlayerForm->createView(),
            'added_players' => $tournamentPlayers,
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


    #[Route('/tournament/{tournamentId}/remove_player/{username}', name: 'tournament_remove_player', methods: ['POST'])]
    public function removePlayer(Request $request, SessionInterface $session, $tournamentId, $username)
    {
        $sessionKey = 'added_players_' . $tournamentId;
        $tournamentPlayers = $session->get($sessionKey, []);

        $index = array_search($username, $tournamentPlayers);
        if ($index !== false) {
            unset($tournamentPlayers[$index]);
            $tournamentPlayers = array_values($tournamentPlayers);
            $session->set($sessionKey, $tournamentPlayers);
        }

        return new JsonResponse(['success' => true, 'remainingPlayers' => $tournamentPlayers]);
    }


    #[Route('/tournament/swap_players', name: 'tournament_swap_players', methods: ['POST'])]
    public function swapPlayers(Request $request, SessionInterface $session)
    {
        //todo
    }


    #[Route('/tournament/{id}/validate_teams', name: 'tournament_validate_teams', methods: ['POST'])]
    public function validateTeams(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $tournament = $entityManager->getRepository(Tournament::class)->find($id);
        if (!$tournament) {
            $this->addFlash('error', 'Tournament not found.');
            return $this->redirectToRoute('home'); // Assuming 'home' as a fallback route
        }

        $data = json_decode($request->getContent(), true);
        $entityManager->getConnection()->beginTransaction(); // Start transaction

        try {
            foreach ($data['teams'] as $teamData) {
                if (empty($teamData['name'])) {
                    throw new \Exception('Team name cannot be empty.'); // Throw to handle in the catch block
                }

                $team = new Team();
                $team->setName($teamData['name']);
                $team->setTournament($tournament);
                $team->setCreatedAt(new \DateTimeImmutable());
                $entityManager->persist($team);

                foreach ($teamData['players'] as $username) {
                    $user = $entityManager->getRepository(User::class)->findOneByUsername($username);
                    if ($user) {
                        $team->addEnrolledPlayer($user);
                    } else {
                        throw new \Exception("Player with username {$username} not found.");
                    }
                }
            }

            $entityManager->flush();
            $entityManager->getConnection()->commit();
            $tournament->setStatus(Tournament::STATUS_SELECT_MATCHES);
            return $this->redirectToRoute('generate_preliminary_matches', ['id' => $id]);
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollBack();
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            return $this->redirectToRoute('tournament_add_players', ['id' => $id]);
        }
    }


    #[Route('/tournament/{id}/generate_preliminary_matches', name: 'generate_preliminary_matches')]
    public function generatePreliminaryMatches(Tournament $tournament, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $teams = $entityManager->getRepository(Team::class)->findBy(['tournament' => $tournament]);
        if ($tournament->getTypeTournament() === 'single_elimination') {
            $matches = $this->setupSingleElimination($teams);
        } else {
            // Autres types de tournois
            $matches = [];
        }

        // Pass the preliminary matches to the session or directly to the template for preview
        $session->set('preliminary_matches', $matches);

        return $this->render('tournament/preliminary_matches.html.twig', [
            'tournament' => $tournament,
            'matches' => $session->get('preliminary_matches', [])
        ]);
    }


    private function setupSingleElimination(array $teams)
    {
        shuffle($teams);
        $matches = [];
        $numberOfTeams = count($teams);

        while (count($teams) > 1) {
            $team1 = array_shift($teams);
            $team2 = array_shift($teams);

            $matches[] = [
                'team1' => ['id' => $team1->getId(), 'name' => $team1->getName()],
                'team2' => ['id' => $team2->getId(), 'name' => $team2->getName()]
            ];
        }

        return $matches;
    }


    #[Route('/tournament/{id}/validate_matches', name: 'validate_matches', methods: ['POST'])]
    public function validateAndSaveMatches(Request $request, Tournament $tournament, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $preliminaryMatches = $session->get('preliminary_matches', []);

        $matchesData = $request->request->all();

        if (empty($matchesData['matches'])) {
            $this->addFlash('error', 'No matches to validate.');
            return $this->redirectToRoute('', ['id' => $tournament->getId()]);
        }

        $entityManager->getConnection()->beginTransaction();
        try {
            foreach ($matchesData['matches'] as $matchData) {
                if (empty($matchData['team1_id']) || empty($matchData['team2_id'])) {
                    throw new \Exception('Match data must include two teams.');
                }
                $teamRepository = $entityManager->getRepository(Team::class);

                $team1 = $teamRepository->find($matchData['team1_id']);
                $team2 = $teamRepository->find($matchData['team2_id']);

                if (!$team1 || !$team2) {
                    throw new \Exception("One of the teams could not be found.");
                }
                $match = new Meeting();
                $match->setTournament($tournament);
                $match->setName('Round: Preliminary');
                $match->setStartTime(new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $match->setCreatedAt(new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $match->addEnrolledTeam($team1);
                $match->addEnrolledTeam($team2);

                $entityManager->persist($match);
            }

            $entityManager->flush();
            $entityManager->getConnection()->commit();

            $this->addFlash('success', 'Matches successfully validated and saved.');
            $tournament->setStatus(Tournament::STATUS_IN_PROGRESS);
            return $this->redirectToRoute('tournament_show', ['id' => $tournament->getId()]);
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollBack();
            $this->addFlash('error', 'Error validating matches: ' . $e->getMessage());
            return $this->redirectToRoute('generate_preliminary_matches', ['id' => $tournament->getId()]);
        }
    }


    #[Route('/tournament/{id}', name: 'tournament_show')]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $tournament = $entityManager->getRepository(Tournament::class)->find($id);
    
        if (!$tournament) {
            throw $this->createNotFoundException('The tournament does not exist');
        }
    
        // Retrieve matches associated with the tournament
        $matches = $tournament->getEnrolledMeetings();
        $bracketData = $this->organizeBracketData($matches);
    
        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament,
            'bracketData' => $bracketData
        ]);
    }
    
    private function organizeBracketData($matches): array
    {
        $bracketData = [];
        foreach ($matches as $match) {
            $round = $match->getName(); // Assumes that the round name is set in the match name
            $bracketData[$round][] = $match;
        }
        ksort($bracketData); // Sort rounds in order
        return $bracketData;
    }
}
