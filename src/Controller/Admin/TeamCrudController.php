<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TeamCrudController extends AbstractCrudController
{
    use Trait\ReadOnlyTrait;
    #[IsGranted('ROLE_ADMIN')]
    public static function getEntityFqcn(): string
    {
        return Team::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('tournament')->setLabel('Tournoi'),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('description')->setLabel('Description'),
            TextField::new('division')->setLabel('Division'),
            IntegerField::new('rating')->setLabel('Classement'),
            AssociationField::new('meetings')->onlyOnIndex()->setLabel('Rencontres'),
            AssociationField::new('enrolled_players')->onlyOnIndex()->setLabel('Joueurs'),
            ArrayField::new('meetings')->onlyOnDetail()->setLabel('Rencontres'),
            ArrayField::new('enrolled_players')->onlyOnDetail()->setLabel('Joueurs'),
        ];
    }
}
