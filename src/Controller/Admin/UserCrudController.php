<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserCrudController extends AbstractCrudController
{
    use Trait\ReadAndDeleteTrait;
    #[IsGranted('ROLE_ADMIN')]
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            //Index
            IdField::new('id')->hideOnForm(),
            ArrayField::new('roles')->setLabel('Roles'),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('username')->setLabel('Pseudo'),
            EmailField::new('email'),
            DateField::new('birth_date')->setLabel('Date de naissance'),
            AssociationField::new('gender')->setLabel('Genre'),
            AssociationField::new('tournaments_organised')->onlyOnIndex()->setLabel('Tournois organisés'),
            AssociationField::new('tournaments_participated')->onlyOnIndex()->setLabel('Tournois participés'),
            AssociationField::new('teams_history')->onlyOnIndex()->setLabel('Equipes'),
            AssociationField::new('meetings_history')->onlyOnIndex()->setLabel('Matchs disputés'),
            
            // Details
            ArrayField::new('tournaments_organised')->onlyOnDetail()->setLabel('Tournois organisés'),
            ArrayField::new('tournaments_participated')->onlyOnDetail()->setLabel('Tournois participés'),
            ArrayField::new('teams_history')->onlyOnDetail()->setLabel('Equipes'),
            ArrayField::new('meetings_history')->onlyOnDetail()->setLabel('Matchs disputés'),

            // Forms

        ];
    }
}
