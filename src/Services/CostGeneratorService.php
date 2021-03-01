<?php


namespace App\Services;


use App\Entity\BudgetAdjustmentDate;
use App\Entity\BudgetDailyAdjustment;
use App\Entity\DailyGeneratedCost;
use App\Repository\BudgetAdjustmentDateRepository;
use App\Repository\BudgetDailyAdjustmentRepository;
use App\Repository\DailyGeneratedCostRepository;
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
    /**
     * @var DailyGeneratedCostRepository
     */
    private $dailyGeneratedCostRepository;

    private $monthlyGeneratedCost;

    private $maxBudgetDaily;

    private $numberOfCostsGeneratedInADay;

    private $dailyGeneratedCosts;

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
        $this->numberOfCostsGeneratedInADay = [];
        $this->dailyGeneratedCosts = [];
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

            $this->maxBudgetDaily = $this->budgetDailyAdjustmentRepository
                ->findMaxBudgetInDay();

            $budgetAdjustmentDays = $this->budgetAdjustmentDateRepository->findBy([], ['day' => 'ASC']);
            foreach ($budgetAdjustmentDays as $budgetAdjustmentDay) {
                $this->generateCostForDay($budgetAdjustmentDay);
            }
        } catch (\Exception $exception) {
            $this->logger->critical(
                sprintf('Could not generate costs. Exceptoin: %s', $exception->getMessage())
            );

            throw new \Exception('Could not generate costs');
        }
    }

    private function generateCostForDay(BudgetAdjustmentDate $budgetAdjustmentDay): void
    {
        $generatedCost = 0;

        $month = intval($budgetAdjustmentDay->getDay()->format('m'));
        $day = $budgetAdjustmentDay->getDay()->format('Y-m-d');

        $remainingBudgetInADay = $this->maxBudgetDaily[$day];

        if (!isset($this->dailyGeneratedCosts[$day])) {
            $this->dailyGeneratedCosts[$day] = 0;
        }

        if (!isset($this->monthlyGeneratedCost[$month])) {
            $this->monthlyGeneratedCost[$month] = 0;
        }


        $dailyAdjustments = $budgetAdjustmentDay->getBudgetDailyAdjustments();
        foreach ($dailyAdjustments as $key => $dailyAdjustment) {
            if ($remainingBudgetInADay <= 0
                || $this->monthlyGeneratedCost[$month] >= $this->monthlyBudget[$month]) {
                break;
            }

            $howManyTimesToGenerateCostsInInterval = intval(10 / count($budgetAdjustmentDay->getBudgetDailyAdjustments()));

            while ($howManyTimesToGenerateCostsInInterval > 0) {

                if (!isset($dailyAdjustments[$key + 1])) {
                    $generatedCost += $this->generateCostInInterval(
                        $budgetAdjustmentDay, $dailyAdjustment, null, $remainingBudgetInADay
                    );
                } else {
                    $generatedCost += $this->generateCostInInterval(
                        $budgetAdjustmentDay, $dailyAdjustment, $dailyAdjustments[$key + 1], $remainingBudgetInADay
                    );
                }

                $remainingBudgetInADay = $remainingBudgetInADay - $generatedCost;

                $howManyTimesToGenerateCostsInInterval--;
            }
        }
    }

    private function generateCostInInterval(
        BudgetAdjustmentDate $budgetAdjustmentDay,
        BudgetDailyAdjustment $adjustment1,
        ?BudgetDailyAdjustment $adjustment2,
        $remainingBudget
    ): int
    {
        if ($adjustment1->getValue() == 0) return 0;

        $day = $budgetAdjustmentDay->getDay()->format('Y-m-d');
        $month = intval($budgetAdjustmentDay->getDay()->format('m'));

        $costsGeneratedInADay = $this->dailyGeneratedCosts[$day];
        $randomTimeInInterval = $this->getRandomTimeInInterval($adjustment1, $adjustment2);

        if (!isset($this->monthlyGeneratedCost[$month])) {
            $this->monthlyGeneratedCost[$month] = 0;
        }

        if ($costsGeneratedInADay > 0) {
            $randomCost = rand(1, $remainingBudget);
        } else {
            $randomCost = rand(1, $remainingBudget * 2);
        }

        if ($randomCost + $this->monthlyGeneratedCost[$month] <= $this->monthlyBudget[$month]) {

            $this->insertCost(
                $budgetAdjustmentDay,
                $randomTimeInInterval,
                $randomCost
            );

            $this->monthlyGeneratedCost[$month] = $this->monthlyGeneratedCost[$month] + $randomCost;
            $this->dailyGeneratedCosts[$day] = $this->dailyGeneratedCosts[$day] + $randomCost;

            return $randomCost;
        }

        return 0;
    }

    private function getRandomTimeInInterval(
        BudgetDailyAdjustment $adjustment1,
        ?BudgetDailyAdjustment $adjustment2
    ): \DateTimeInterface
    {
        $timeStamp1 = $adjustment1->getTime()->getTimestamp();
        if (is_null($adjustment2)) {
            $time2 = new \DateTime('00:00:00');
            $timeStamp2 = $time2->getTimestamp();
        } else {
            $timeStamp2 = $adjustment2->getTime()->getTimestamp();
        }

        $date = new \DateTime();
        $date->setTimestamp(
            rand($timeStamp1, $timeStamp2)
        );

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