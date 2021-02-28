<?php


namespace App\Services;


use App\Entity\BudgetAdjustmentDate;
use App\Entity\BudgetDailyAdjustment;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class BudgetDailyAdjustmentGeneratorService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function truncateTables(): void
    {
        try {
            $connection = $this->entityManager->getConnection();
            $platform = $connection->getDatabasePlatform();
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->executeUpdate($platform->getTruncateTableSQL('budget_adjustment_date', true /* whether to cascade */));
            $connection->executeUpdate($platform->getTruncateTableSQL('budget_daily_adjustment', true /* whether to cascade */));
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $exception) {
            $this->logger->critical(
                sprintf('Could not truncate tables. Exception: %s', $exception->getMessage())
            );

            throw new \Exception('Could not truncate tables.');
        }
    }

    public function generateBudget(): void
    {
        try {
            $dateNow = new \DateTime();

            $dateNowPlusThreeMonths = new \DateTime();
            $dateNowPlusThreeMonths->add(new \DateInterval('P3M'));

            $randomAdjustmentDates = $this->getRandomDaysFromMonthsInterval(
                10, $dateNow, $dateNowPlusThreeMonths
            );

            foreach ($randomAdjustmentDates as $randomAdjustmentDate) {
                $adjustmentsPerDay = mt_rand(0, 10);
                $this->generateDailyBudgetAdjustments($adjustmentsPerDay, $randomAdjustmentDate);
            }
        } catch (\Exception $exception) {
            $this->logger->critical(
                sprintf('Could not generate budget. Exception: %s', $exception->getMessage())
            );

            throw new \Exception('Could not generate budget.');
        }
    }

    private function generateDailyBudgetAdjustments(
        int $adjustmentsPerDay,
        \DateTimeInterface $randomAdjustmentDate
    ): void
    {
        $budgetAdjustmentDate = new BudgetAdjustmentDate();
        $budgetAdjustmentDate->setDay($randomAdjustmentDate);

        while ($adjustmentsPerDay > 0) {
            $randomAdjustmentDate->add(new \DateInterval(sprintf("PT%sH", $adjustmentsPerDay)));

            $dailyAdjustment = new BudgetDailyAdjustment();
            $dailyAdjustment->setTime($randomAdjustmentDate);
            $dailyAdjustment->setValue(rand(0, 10));

            $budgetAdjustmentDate->addBudgetDailyAdjustment($dailyAdjustment);

            $this->entityManager->persist($dailyAdjustment);
            $this->entityManager->flush();

            $adjustmentsPerDay--;
        }
    }

    private function getRandomDaysFromMonthsInterval(
        int $numberOfDays,
        \DateTimeInterface $month1,
        \DateTimeInterface $month2
    ): array
    {
        $randomDates = [];
        while ($numberOfDays > 0) {
            $date = new \DateTime();
            $randomDates[] = $date->setTimestamp(
                rand($month1->getTimestamp(), $month2->getTimestamp())
            );
            $numberOfDays--;
        }

        return $randomDates;
    }
}