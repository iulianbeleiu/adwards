<?php


namespace App\Services;


use App\Entity\BudgetAdjustmentDate;
use App\Entity\DailyGeneratedCost;
use App\Repository\BudgetAdjustmentDateRepository;
use App\Repository\BudgetDailyAdjustmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CostGeneratorService
{
    /**
     * @var BudgetAdjustmentDateRepository
     */
    private $budgetAdjustmentDateRepository;
    /**
     * @var BudgetDailyAdjustmentRepository
     */
    private $budgetDailyAdjustmentRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $monthlyBudget;

    public function __construct(
        BudgetAdjustmentDateRepository $budgetAdjustmentDateRepository,
        BudgetDailyAdjustmentRepository $budgetDailyAdjustmentRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->budgetAdjustmentDateRepository = $budgetAdjustmentDateRepository;
        $this->budgetDailyAdjustmentRepository = $budgetDailyAdjustmentRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function truncateTables(): void
    {
        try {
            $connection = $this->entityManager->getConnection();
            $platform = $connection->getDatabasePlatform();
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->executeUpdate($platform->getTruncateTableSQL('daily_generated_cost', true /* whether to cascade */));
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $exception) {
            $this->logger->critical(
                sprintf('Could not truncate tables. Exception: %s', $exception->getMessage())
            );

            throw new \Exception('Could not truncate tables.');
        }
    }

    public function generateCosts(): void
    {
        try {
            $this->monthlyBudget = $this->budgetDailyAdjustmentRepository
                ->findTotalBudgetMonthly();

            $budgetAdjustments = $this->budgetAdjustmentDateRepository->findBy([], ['day' => 'ASC']);
            $maxBudgetDaily = $this->budgetDailyAdjustmentRepository
                ->findMaxBudgetInDay();

            foreach ($budgetAdjustments as $budgetAdjustment) {
                $this->generateCostForDayWithBudgetAdjustments($budgetAdjustment, $maxBudgetDaily);
            }
        } catch (\Exception $exception) {
            $this->logger->critical(
                sprintf('Could not generate costs. Exceptoin: %s', $exception->getMessage())
            );

            throw new \Exception('Could not generate costs');
        }
    }


    private function generateCostForDayWithBudgetAdjustments(
        BudgetAdjustmentDate $budgetDate,
        array $maxBudgetDaily
    ): void
    {
        $maxBudgetPerDay = $maxBudgetDaily[$budgetDate->getDay()->format('Y-m-d')];

        $dailyAdjustments = $budgetDate->getBudgetDailyAdjustments();
        $month = intval($budgetDate->getDay()->format('m'));
        $monthlyBudget = $this->monthlyBudget;

        $costsGeneratedInADay = 0;

        foreach ($dailyAdjustments as $dailyAdjustment) {

            if ($dailyAdjustment->getValue() == 0) {
                continue;
            }

            if ($costsGeneratedInADay > 0) {
                $randomCost = rand(1, $maxBudgetPerDay - $costsGeneratedInADay);
            } else {
                $randomCost = rand(1, $maxBudgetPerDay * 2);
            }

            if ($randomCost <= $monthlyBudget[$month]) {
                $this->insertCost(
                    $budgetDate,
                    $dailyAdjustment->getTime()->add(new \DateInterval('PT30M')),
                    $randomCost
                );

                $costsGeneratedInADay += $randomCost;
            }
        }

    }

    private function insertCost(
        BudgetAdjustmentDate $budgetDate,
        \DateTimeInterface $time,
        float $costValue
    ): void
    {
        $dailyGeneratedCost = new DailyGeneratedCost();
        $dailyGeneratedCost->setTime($time);
        $dailyGeneratedCost->setValue($costValue);

        $dailyGeneratedCost->setBudgetDate($budgetDate);

        $this->entityManager->persist($dailyGeneratedCost);
        $this->entityManager->flush();
    }
}