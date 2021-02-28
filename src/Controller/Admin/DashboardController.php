<?php

namespace App\Controller\Admin;

use App\Entity\BudgetAdjustmentDate;
use App\Entity\BudgetDailyAdjustment;
use App\Entity\DailyGeneratedCost;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(BudgetAdjustmentDateCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('AdWords');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Budget'),
            MenuItem::linkToCrud('Budget date', 'fas fa-user', BudgetAdjustmentDate::class),
            MenuItem::linkToCrud('Budget daily adjustments', 'fas fa-user', BudgetDailyAdjustment::class),

            MenuItem::section('Costs'),
            MenuItem::linkToCrud('Daily generated costs', 'fas fa-user', DailyGeneratedCost::class),
        ];
    }
}
