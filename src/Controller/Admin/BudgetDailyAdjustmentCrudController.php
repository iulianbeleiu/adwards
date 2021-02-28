<?php

namespace App\Controller\Admin;

use App\Entity\BudgetDailyAdjustment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class BudgetDailyAdjustmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BudgetDailyAdjustment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TimeField::new('time'),
            AssociationField::new('budgetDate')
        ];
    }
}
