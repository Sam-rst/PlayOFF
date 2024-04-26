<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\Team;
use App\Form\MeetingType;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/meeting')]
class MeetingController extends AbstractController
{
    #[Route('/', name: 'app_meeting_index', methods: ['GET'])]
    public function index(MeetingRepository $meetingRepository): Response
    {
        return $this->render('meeting/index.html.twig', [
            'meetings' => $meetingRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_meeting_show', methods: ['GET'])]
    public function show(Meeting $meeting): Response
    {
        return $this->render('meeting/show.html.twig', [
            'meeting' => $meeting,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_meeting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Meeting $meeting, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $meeting = $form->getData();
                $score = $form->get('score')->getData();
                $score = explode(',', $score);
                $score = array_filter($score);
                $score = array_map('intval', $score);
                $score = array_unique($score);

                // Maintenant, assignez $score à l'entité Meeting
                $meeting->setScore($score);

                $entityManager->persist($meeting);
                $entityManager->flush();

                $this->addFlash('success', 'Le meeting a été mis à jour avec succès.');

                return $this->redirectToRoute('app_meeting_show', ['id' => $meeting->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du meeting : ' . $e->getMessage());
        }

        $teams = $meeting->getEnrolledTeams();
        $tournament = $meeting->getTournament();

        return $this->render('meeting/edit.html.twig', [
            'meeting' => $meeting,
            'form' => $form->createView(),
            'teams' => $teams,
            'tournament' => $tournament,
        ]);
    }


    #[Route('/meeting/update-ranking', name: 'meeting_update_ranking', methods: ['POST'])]
    public function updateRanking(Request $request, EntityManagerInterface $entityManager): Response {
        $data = json_decode($request->getContent(), true);
        $teamId = $data['teamId'];
        $rank = $data['rank'];
        $meetingId = $data['meetingId']; 
    
        $team = $entityManager->getRepository(Team::class)->find($teamId);
        if (!$team) {
            return new Response('Team not found', Response::HTTP_NOT_FOUND);
        }
    
        // Assumons que vous avez déjà une instance de Meeting
        $meeting = $entityManager->getRepository(Meeting::class)->find(1); // Trouvez votre Meeting selon le contexte nécessaire
        if (!$meeting) {
            return new Response('Meeting not found', Response::HTTP_NOT_FOUND);
        }
    
        $meeting->addRanking($team);
        $entityManager->flush();
    
        return new Response(json_encode(['success' => true]), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

}
// [Application] Apr 26 06:56:51 |INFO   | DOCTRI Connecting with parameters array{"use_savepoints":true,"driver":"pdo_mysql","host":"127.0.0.1","port":3306,"user":"root","password":null,"driverOptions":[],"defaultTableOptions":[],"dbname":"PlayOff","serverVersion":"8.0.32","charset":"utf8mb4"} params={"charset":"utf8mb4","dbname":"PlayOff","defaultTableOptions":[],"driver":"pdo_mysql","driverOptions":[],"host":"127.0.0.1","password":null,"port":3306,"serverVersion":"8.0.32","use_savepoints":true,"user":"root"}
// [Application] Apr 26 06:56:51 |DEBUG  | DOCTRI Executing statement: SELECT t0.id AS id_1, t0.name AS name_2, t0.start_time AS start_time_3, t0.end_time AS end_time_4, t0.score AS score_5, t0.win_condition AS win_condition_6, t0.created_at AS created_at_7, t0.tournament_id AS tournament_id_8 FROM meeting t0 WHERE t0.id = ? (parameters: array{"1":"14"}, types: array{"1":1}) params={"1":"14"} sql="SELECT t0.id AS id_1, 
// t0.name AS name_2, t0.start_time AS start_time_3, t0.end_time AS end_time_4, t0.score AS score_5, t0.win_condition AS win_condition_6, t0.created_at AS created_at_7, t0.tournament_id AS tournament_id_8 FROM meeting t0 WHERE t0.id = ?" types={"1":1}
// [Application] Apr 26 06:56:52 |DEBUG  | SECURI Read existing security token from the session. key="_security_main" token_class="Symfony\\Component\\Security\\Http\\Authenticator\\Token\\PostAuthenticationToken"[Application] Apr 26 06:56:52 |DEBUG  | DOCTRI Executing statement: SELECT t0.id AS id_1, t0.email AS email_2, t0.roles AS roles_3, t0.password AS password_4, t0.firstname AS firstname_5, t0.lastname AS lastname_6, t0.username AS username_7, t0.birth_date AS birth_date_8, t0.created_at AS created_at_9, t0.updated_at AS updated_at_10, t0.gender_id AS gender_id_11 FROM user t0 WHERE t0.id = ? (parameters: array{"1":104}, types: array{"1":1}) params={"1":104} sql="SELECT t0.id AS id_1, t0.email AS email_2, t0.roles AS roles_3, t0.password AS password_4, t0.firstname AS firstname_5, t0.lastname AS lastname_6, t0.username AS username_7, t0.birth_date AS birth_date_8, t0.created_at AS created_at_9, t0.updated_at AS updated_at_10, t0.gender_id AS gender_id_11 FROM user t0 WHERE t0.id = ?" types={"1":1}
// [Application] Apr 26 06:56:52 |DEBUG  | SECURI User was reloaded from a user provider. provider="Symfony\\Bridge\\Doctrine\\Security\\User\\EntityUserProvider" username="paul.claverie@live.fr"
// [Application] Apr 26 06:56:52 |DEBUG  | SECURI Skipping the "Symfony\Component\Security\Http\Authenticator\RememberMeAuthenticator" authenticator as it did not support the request. authenticator="Symfony\\Component\\Security\\Http\\Authenticator\\RememberMeAuthenticator"
// [Application] Apr 26 06:56:52 |DEBUG  | DOCTRI Executing statement: SELECT t0.id AS id_1, t0.name AS name_2, t0.rules AS rules_3, t0.is_public AS is_public_4, t0.start_time AS start_time_5, t0.end_time AS end_time_6, t0.number_players_per_team AS number_players_per_team_7, t0.type_tournament AS type_tournament_8, 
// t0.created_at AS created_at_9, t0.status AS status_10, t0.sport_id AS sport_id_11, t0.organisator_id AS organisator_id_12, t0.gender_rule_id AS gender_rule_id_13 FROM tournament t0 WHERE t0.id = ? (parameters: 
// array{"1":16}, types: array{"1":1}) params={"1":16} sql="SELECT t0.id AS id_1, t0.name AS name_2, t0.rules AS rules_3, t0.is_public AS is_public_4, t0.start_time AS start_time_5, t0.end_time AS end_time_6, t0.number_players_per_team AS number_players_per_team_7, t0.type_tournament AS type_tournament_8, t0.created_at AS created_at_9, t0.status AS status_10, t0.sport_id AS sport_id_11, t0.organisator_id AS organisator_id_12, t0.gender_rule_id AS gender_rule_id_13 FROM tournament t0 WHERE t0.id = ?" types={"1":1}
// [Application] Apr 26 06:56:52 |DEBUG  | DOCTRI Executing statement: SELECT t0.id AS id_1, t0.name AS name_2, t0.description AS description_3, t0.division AS division_4, t0.rating AS rating_5, t0.created_at AS created_at_6, t0.tournament_id AS tournament_id_7, t0.rank_meeting_id AS rank_meeting_id_8 FROM team t0 INNER JOIN meeting_team ON t0.id = meeting_team.team_id WHERE meeting_team.meeting_id = ? (parameters: array{"1":14}, types: array{"1":1}) params={"1":14} sql="SELECT t0.id AS id_1, t0.name AS name_2, t0.description AS description_3, t0.division AS division_4, t0.rating AS rating_5, t0.created_at AS created_at_6, t0.tournament_id AS tournament_id_7, t0.rank_meeting_id AS rank_meeting_id_8 FROM team t0 INNER JOIN meeting_team ON t0.id = meeting_team.team_id WHERE meeting_team.meeting_id = ?"
// [Application] Apr 26 06:56:52 |DEBUG  | SECURI Stored the security token in the session. key="_security_main"
// [Web Server ] Apr 26 08:56:52 |INFO   | SERVER GET  (304) /js/edit_meeting.js ip="127.0.0.1"
// [Web Server ] Apr 26 08:56:52 |INFO   | SERVER GET  (200) /meeting/14/edit 
// [Application] Apr 26 06:56:52 |INFO   | DOCTRI Disconnecting 
// [Application] Apr 26 06:56:52 |DEBUG  | PHP    User Warning: Configure the "curl.cainfo", "openssl.cafile" or "openssl.capath" php.ini setting to enable the CurlHttpClient
// [Application] Apr 26 06:56:52 |DEBUG  | PHP    User Notice: Upgrade the curl extension or run "composer require amphp/http-client:^4.2.1" to perform async HTTP operations, including full HTTP/2 support
// {"level":"error","time":"2024-04-26T08:56:52+02:00","message":"command \"PHP-CGI\" exited, restarting it 
// immediately"}
// [Web Server ] Apr 26 08:56:52 |INFO   | SERVER GET  (200) /assets/styles/app-6bd4b02cf184ca41fdeefb63a6f3add2.scss ip="127.0.0.1"
// [Web Server ] Apr 26 08:56:52 |ERROR  | SERVER GET  (502) /_wdt/63c8f2 
// [Application] Apr 26 06:56:53 |DEBUG  | PHP    User Warning: Configure the "curl.cainfo", "openssl.cafile" or "openssl.capath" php.ini setting to enable the CurlHttpClient
// [Application] Apr 26 06:56:53 |DEBUG  | PHP    User Notice: Upgrade the curl extension or run "composer require amphp/http-client:^4.2.1" to perform async HTTP operations, including full HTTP/2 support
// [Application] Apr 26 06:56:54 |INFO   | REQUES Matched route "meeting_update_ranking". method="POST" request_uri="http://127.0.0.1:8000/meeting/meeting/update-ranking" route="meeting_update_ranking" route_parameters={"_controller":"App\\Controller\\MeetingController::updateRanking","_route":"meeting_update_ranking"}
// [Application] Apr 26 06:56:54 |DEBUG  | SECURI Read existing security token from the session. key="_security_main" token_class="Symfony\\Component\\Security\\Http\\Authenticator\\Token\\PostAuthenticationToken"[Application] Apr 26 06:56:54 |INFO   | DOCTRI Connecting with parameters array{"use_savepoints":true,"driver":"pdo_mysql","host":"127.0.0.1","port":3306,"user":"root","password":null,"driverOptions":[],"defaultTableOptions":[],"dbname":"PlayOff","serverVersion":"8.0.32","charset":"utf8mb4"} params={"charset":"utf8mb4","dbname":"PlayOff","defaultTableOptions":[],"driver":"pdo_mysql","driverOptions":[],"host":"127.0.0.1","password":null,"port":3306,"serverVersion":"8.0.32","use_savepoints":true,"user":"root"}
// [Application] Apr 26 06:56:54 |DEBUG  | DOCTRI Executing statement: SELECT t0.id AS id_1, t0.email AS email_2, t0.roles AS roles_3, t0.password AS password_4, t0.firstname AS firstname_5, t0.lastname AS lastname_6, t0.username AS username_7, t0.birth_date AS birth_date_8, t0.created_at AS created_at_9, t0.updated_at AS updated_at_10, t0.gender_id AS gender_id_11 FROM user t0 WHERE t0.id = ? (parameters: array{"1":104}, types: array{"1":1}) params={"1":104} sql="SELECT t0.id AS id_1, t0.email AS email_2, t0.roles AS roles_3, t0.password AS password_4, t0.firstname AS firstname_5, t0.lastname AS lastname_6, t0.username AS username_7, t0.birth_date AS birth_date_8, t0.created_at AS created_at_9, t0.updated_at AS updated_at_10, t0.gender_id AS gender_id_11 FROM user t0 WHERE t0.id = ?" types={"1":1}
// [Application] Apr 26 06:56:54 |DEBUG  | SECURI User was reloaded from a user provider. provider="Symfony\\Bridge\\Doctrine\\Security\\User\\EntityUserProvider" username="paul.claverie@live.fr"
// [Application] Apr 26 06:56:54 |DEBUG  | SECURI Checking for authenticator support. authenticators=2 firewall_name="main"
// [Application] Apr 26 06:56:54 |DEBUG  | SECURI Checking support on authenticator. authenticator="App\\Security\\UsersAuthenticator"
// [Application] Apr 26 06:56:54 |DEBUG  | SECURI Authenticator does not support the request.
// [Application] Apr 26 06:56:54 |DEBUG  | SECURI Checking support on authenticator. authenticator="Symfony\\Component\\Security\\Http\\Authenticator\\RememberMeAuthenticator"
// [Application] Apr 26 06:56:54 |DEBUG  | SECURI Authenticator does not support the request.
// [Web Server ] Apr 26 08:56:54 |INFO   | SERVER POST (200) /meeting/meeting/update-ranking host="127.0.0.1:8000" ip="127.0.0.1" scheme="https"
// [Application] Apr 26 06:56:54 |DEBUG  | DOCTRI Executing statement: SELECT t0.id AS id_1, t0.name AS name_2, t0.description AS description_3, t0.division AS division_4, t0.rating AS rating_5, t0.created_at AS created_at_6, t0.tournament_id AS tournament_id_7, t0.rank_meeting_id AS rank_meeting_id_8 FROM team t0 WHERE t0.id = ? (parameters: array{"1":"52"}, types: array{"1":1}) params={"1":"52"} sql="SELECT t0.id AS id_1, t0.name AS name_2, t0.description AS description_3, t0.division AS division_4, t0.rating AS rating_5, t0.created_at AS created_at_6, t0.tournament_id AS tournament_id_7, t0.rank_meeting_id AS rank_meeting_id_8 FROM team t0 WHERE t0.id = ?" types={"1":1}
// [Application] Apr 26 06:56:54 |DEBUG  | DOCTRI Executing statement: SELECT t0.id AS id_1, t0.name AS name_2, t0.start_time AS start_time_3, t0.end_time AS end_time_4, t0.score AS score_5, t0.win_condition AS win_condition_6, t0.created_at AS created_at_7, t0.tournament_id AS tournament_id_8 FROM meeting t0 WHERE t0.id = ? (parameters: array{"1":1}, types: array{"1":1}) params={"1":1} sql="SELECT t0.id AS id_1, t0.name AS name_2, t0.start_time AS start_time_3, t0.end_time AS end_time_4, t0.score AS score_5, t0.win_condition AS win_condition_6, t0.created_at AS created_at_7, t0.tournament_id AS tournament_id_8 FROM meeting t0 WHERE t0.id = ?"
// [Application] Apr 26 06:56:54 |DEBUG  | DOCTRI Executing statement: SELECT t0.id AS id_1, t0.name AS name_2, t0.description AS description_3, t0.division AS division_4, t0.rating AS rating_5, t0.created_at AS created_at_6, t0.tournament_id AS tournament_id_7, t0.rank_meeting_id AS rank_meeting_id_8 FROM team t0 WHERE t0.rank_meeting_id = ? (parameters: array{"1":1}, types: array{"1":1}) sql="SELECT t0.id AS id_1, t0.name AS name_2, t0.description AS description_3, t0.division AS division_4, t0.rating AS rating_5, t0.created_at AS created_at_6, t0.tournament_id AS tournament_id_7, t0.rank_meeting_id AS rank_meeting_id_8 FROM team t0 WHERE t0.rank_meeting_id = ?"
// [Application] Apr 26 06:56:54 |DEBUG  | SECURI Stored the security token in the session. key="_security_main"