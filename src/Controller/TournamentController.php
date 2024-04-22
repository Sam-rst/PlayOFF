<?php

// src/Controller/TournamentController.php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            return $this->redirectToRoute('tournament_success');
        }
    
        // If the form is not submitted or not valid, render the form
        return $this->render('tournament/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/tournament/success', name: 'tournament_success')]
    public function success(): Response
    {
        return $this->render('tournament/succes.html.twig');
    }
}
