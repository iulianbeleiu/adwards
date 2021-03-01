<?php

namespace App\Controller;

use App\Repository\DailyGeneratedCostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CostsChartController extends AbstractController
{
    /**
     * @Route("/", name="costs_chart")
     */
    public function index(): Response
    {
        return $this->render('costs_chart/index.html.twig');
    }

    /**
     * @Route("/daily-report")
     */
    public function dailyReport(DailyGeneratedCostRepository $dailyGeneratedCostRepository)
    {
        $dailyBudgetAndCosts = $dailyGeneratedCostRepository->findMaxBudgetAndGeneratedCostsPerDay();

        $budget = [];
        $costs = [];
        $days = [];
        foreach ($dailyBudgetAndCosts as $key => $dailyBudgetAndCost) {
            $budget[] = $dailyBudgetAndCost['max_per_day'];
            $costs[] = $dailyBudgetAndCost['generated_cost'];
            $days[] = $dailyBudgetAndCost['day'];
        }

        return new JsonResponse([
            'budget' => $budget,
            'costs' => $costs,
            'days' => $days
        ]);
    }
}
