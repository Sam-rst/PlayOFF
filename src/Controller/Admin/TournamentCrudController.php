<?php

namespace App\Controller\Admin;

use App\Entity\Tournament;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\ArrayKey;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TournamentCrudController extends AbstractCrudController
{
    use Trait\ReadOnlyTrait;
    #[IsGranted('ROLE_ADMIN')]
    public static function getEntityFqcn(): string
    {
        return Tournament::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            // Index
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name')->setLabel("Nom"),
            AssociationField::new('organisator')->setLabel("Organisateur"),
            AssociationField::new('sport')->setLabel("Sport"),
            TextField::new('type_tournament')->setLabel("Type"),
            IntegerField::new('number_players_per_team')->setLabel("Nbr joueurs/équipe")->setColumns("col-8"),
            BooleanField::new('is_public')->setLabel("Publique")->setDisabled(),
            AssociationField::new('participating_players')->onlyOnIndex()->setLabel("Nbr joueurs inscrits"),
            AssociationField::new('enrolled_teams')->onlyOnIndex()->setLabel("Nbr équipes"),

            TextEditorField::new('rules')->onlyOnDetail()->setLabel("Règles"),
            ArrayField::new('participating_players')->onlyOnDetail()->setLabel("Joueurs inscrits"),
            ArrayField::new('enrolled_teams')->onlyOnDetail()->setLabel("Noms des équipes"),
        ];
    }
}
