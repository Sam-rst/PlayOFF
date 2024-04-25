<?php

namespace App\Controller\Admin;

use App\Entity\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MeetingCrudController extends AbstractCrudController
{
    use Trait\ReadOnlyTrait;
    #[IsGranted('ROLE_ADMIN')]
    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name')->setLabel('Nom'),
            DateTimeField::new('start_time')->setFormat('dd MMMM yyyy à hh:mm:ss')->setLabel('Début de la rencontre'),
            DateTimeField::new('end_time')->setFormat('dd MMMM yyyy à hh:mm:ss')->setLabel('Fin de la rencontre'),
            TextField::new('Winner')->setLabel('Gagnant'),
            ArrayField::new('score')->setLabel('Score'),
            ArrayField::new('ranking')->setLabel('Classement'),
            TextField::new('win_condition')->setLabel('Gagné comment ?'),
            AssociationField::new('enrolled_teams')->setLabel('Equipes')->onlyOnIndex(),
            ArrayField::new('enrolled_teams')->setLabel('Equipes')->onlyOnDetail(),
        ];
    }
}
