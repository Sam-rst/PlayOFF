<?php

namespace App\Controller\Admin;

use App\Entity\Sport;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SportCrudController extends AbstractCrudController
{
    #[IsGranted('ROLE_ADMIN')]
    public static function getEntityFqcn(): string
    {
        return Sport::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('description')->setLabel('Description'),
            AssociationField::new('tournaments')->setLabel('Tournois')->onlyOnIndex(),
            ArrayField::new('tournaments')->setLabel('Tournois')->onlyOnDetail(),
        ];
    }
}
