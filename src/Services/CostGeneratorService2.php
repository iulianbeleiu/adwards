<?php


namespace App\Services;


use App\Entity\BudgetAdjustmentDate;
use App\Entity\DailyGeneratedCost;
use App\Repository\BudgetAdjustmentDateRepository;
use App\Repository\BudgetDailyAdjustmentRepository;
use App\Repository\DailyGeneratedCostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CostGeneratorService2
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
    /**
     * @var DailyGeneratedCostRepository
     */
    private $dailyGeneratedCostRepository;

    private $monthlyGeneratedCost;

    public function __construct(
        BudgetAdjustmentDateRepository $budgetAdjustmentDateRepository,
        BudgetDailyAdjustmentRepository $budgetDailyAdjustmentRepository,
        DailyGeneratedCostRepository $dailyGeneratedCostRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->budgetAdjustmentDateRepository = $budgetAdjustmentDateRepository;
        $this->budgetDailyAdjustmentRepository = $budgetDailyAdjustmentRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->dailyGeneratedCostRepository = $dailyGeneratedCostRepository;
        $this->monthlyGeneratedCost = [];
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

            $maxBudgetDaily = $this->budgetDailyAdjustmentRepository
                ->findMaxBudgetInDay();

            $budgetAdjustments = $this->budgetAdjustmentDateRepository->findBy([], ['day' => 'ASC']);
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
        $month = intval($budgetDate->getDay()->format('m'));
        $remainingBudgetInADay = $maxBudgetDaily[$budgetDate->getDay()->format('Y-m-d')];

        if (!isset($this->monthlyGeneratedCost[$month])) {
            $this->monthlyGeneratedCost[$month] = 0;
        }

        $costsGeneratedInADay = 0;

        $dailyAdjustments = $budgetDate->getBudgetDailyAdjustments();

        foreach ($dailyAdjustments as $key => $dailyAdjustment) {
            if ($remainingBudgetInADay <= 0
                || $this->monthlyGeneratedCost[$month] >= $this->monthlyBudget[$month]) {
                break;
            }

            if ($dailyAdjustment->getValue() == 0) {
                continue;
            }

            if ($costsGeneratedInADay > 0) {
                $randomCost = rand(1, $remainingBudgetInADay);
            } else {
                $randomCost = rand(1, $remainingBudgetInADay * 2);
            }

            if ($randomCost + $this->monthlyGeneratedCost[$month] <= $this->monthlyBudget[$month]) {

                if (isset($dailyAdjustments[$key + 1])) {
                    $randomDate = $this->getRandomDateInInterval($dailyAdjustment->getTime(), $dailyAdjustments[$key + 1]->getTime());
                } else {
                    $midnightDate = new \DateTime('00:00:00');
                    $randomDate = $this->getRandomDateInInterval($dailyAdjustment->getTime(), $midnightDate);
                }

                $this->insertCost(
                    $budgetDate,
                    $randomDate,
                    $randomCost
                );

                $this->monthlyGeneratedCost[$month] = $this->monthlyGeneratedCost[$month] + $randomCost;

                $costsGeneratedInADay += $randomCost;
                $remainingBudgetInADay = $remainingBudgetInADay - $costsGeneratedInADay;
            }
        }

    }

    private function getRandomDateInInterval(
        \DateTimeInterface $date1,
        \DateTimeInterface $date2): \DateTimeInterface
    {
        $date = new \DateTime();
        $date->setTimestamp(rand($date1->getTimestamp(), $date2->getTimestamp()));

        return $date;
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