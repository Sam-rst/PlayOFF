<?php

namespace App\Controller\Admin;

use App\Entity\Gender;
use App\Entity\Meeting;
use App\Entity\Sport;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PlayOff');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('User', 'fa-solid fa-user', User::class);
        yield MenuItem::linkToCrud('Tournament', 'fa-solid fa-trophy', Tournament::class);
        yield MenuItem::linkToCrud('Team', 'fa-solid fa-users', Team::class);
        yield MenuItem::linkToCrud('Meeting', 'fa-solid fa-handshake', Meeting::class);
        yield MenuItem::linkToCrud('Sport', 'fa-solid fa-table-tennis-paddle-ball', Sport::class);
        yield MenuItem::linkToCrud('Gender', 'fa-solid fa-venus-mars', Gender::class);
    }
}
