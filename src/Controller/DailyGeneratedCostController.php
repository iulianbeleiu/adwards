<?php

namespace App\Controller;

use App\Services\CostGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DailyGeneratedCostController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/generate-costs", name="generate_costs")
     */
    public function index(
        CostGeneratorService $costGeneratorService
    ): Response
    {
        try {
            $costGeneratorService->truncateTables();
            $costGeneratorService->generateCosts();
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
