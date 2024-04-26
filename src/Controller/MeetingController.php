<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Form\MeetingType;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function edit(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $meeting
                ->setScore(["{$form->getData()->getScore()}"])
                ->setName($form->getData()->getName())
                ->setWinCondition($form->getData()->getWinCondition())
                ->setStartTime($form->getData()->getStartTime())
                ->setEndTime($form->getData()->getEndTime());
            foreach ($meeting->getEnrolledTeams() as $team) {
                $meeting
                    ->addRanking($team);
            }
            $entityManager->persist($meeting);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        $teams = $meeting->getEnrolledTeams();
        $tournament = $meeting->getTournament();

        return $this->render('meeting/edit.html.twig', [
            'meeting' => $meeting,
            'form' => $form,
            'teams' => $teams,
            'tournament' => $tournament,
        ]);
    }
}
