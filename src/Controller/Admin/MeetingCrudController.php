<?php

namespace App\Controller\Admin;

use App\Entity\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MeetingCrudController extends AbstractCrudController
{
    use Trait\ReadOnlyTrait;
    public static function getEntityFqcn(): string
    {
        return Meeting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            DateTimeField::new('start_time')->setFormat('dd MMMM yyyy à hh:mm:ss')->setLabel('Début de la rencontre'),
            DateTimeField::new('end_time')->setFormat('dd MMMM yyyy à hh:mm:ss')->setLabel('Fin de la rencontre'),
        ];
    }
}
