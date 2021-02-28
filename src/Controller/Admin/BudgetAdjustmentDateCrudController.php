<?php

namespace App\Controller\Admin;

use App\Entity\BudgetAdjustmentDate;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class BudgetAdjustmentDateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BudgetAdjustmentDate::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            DateField::new('day'),
            CollectionField::new('budgetDailyAdjustments'),
            CollectionField::new('dailyGeneratedCosts')
        ];
    }
}
