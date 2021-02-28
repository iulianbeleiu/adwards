<?php

namespace App\Controller\Admin;

use App\Entity\DailyGeneratedCost;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class DailyGeneratedCostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DailyGeneratedCost::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TimeField::new('time'),
            NumberField::new('value'),
            AssociationField::new('budgetDate')
        ];
    }
}
