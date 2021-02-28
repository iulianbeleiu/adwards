<?php

namespace App\Controller;

use App\Services\BudgetDailyAdjustmentGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/budget/daily/adjustment")
 */
class BudgetDailyAdjustmentController extends AbstractController
{
    /**
     * @Route("/generate")
     */
    public function generate(BudgetDailyAdjustmentGeneratorService $budgetDailyAdjustmentGeneratorService): Response
    {
        try {
            $budgetDailyAdjustmentGeneratorService->truncateTables();
            $budgetDailyAdjustmentGeneratorService->generateBudget();
        } catch (\Exception $exception) {
            return new JsonResponse([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => sprintf('Error: %s', $exception->getMessage()),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'code' => Response::HTTP_CREATED,
            'message' => 'OK',
        ], Response::HTTP_CREATED);
    }
}
