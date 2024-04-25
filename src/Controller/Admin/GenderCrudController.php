<?php

namespace App\Controller\Admin;

use App\Entity\Gender;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GenderCrudController extends AbstractCrudController
{
    #[IsGranted('ROLE_ADMIN')]
    public static function getEntityFqcn(): string
    {
        return Gender::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('gender')->setLabel('Genre'),
            AssociationField::new('users')->setLabel('Utilisateurs'),
            AssociationField::new('tournaments')->setLabel('Tournois'),
        ];
    }
}
