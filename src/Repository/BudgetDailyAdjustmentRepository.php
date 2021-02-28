<?php

namespace App\Repository;

use App\Entity\BudgetDailyAdjustment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BudgetDailyAdjustment|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetDailyAdjustment|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetDailyAdjustment[]    findAll()
 * @method BudgetDailyAdjustment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetDailyAdjustmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetDailyAdjustment::class);
    }

    // /**
    //  * @return BudgetDailyAdjustment[] Returns an array of BudgetDailyAdjustment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BudgetDailyAdjustment
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * Note:
     * Returns array of key=>value (month=>total_budget)
     * Example:
     * ['3' => '18', '4' => '37']
     */
    public function findTotalBudgetMonthly()
    {
        $connection = $this->getEntityManager()
            ->getConnection();
        $sql = '
              SELECT Month(max_budget_daily.day) month,
                       Sum(max_per_day) total_budget
                FROM   (SELECT Max(budget_daily_adjustment.value) max_per_day,
                               budget_adjustment_date.day
                        FROM   budget_daily_adjustment
                               INNER JOIN budget_adjustment_date
                                       ON budget_adjustment_date.id =
                                          budget_daily_adjustment.budget_date_id
                        GROUP  BY budget_adjustment_date.day) max_budget_daily
                GROUP  BY Month(max_budget_daily.day);  
        ';

        $statement = $connection->prepare($sql);
        $statement->execute();

        return $statement->fetchAllKeyValue();
    }

    public function findMaxBudgetInDay()
    {
        $connection = $this->getEntityManager()
            ->getConnection();
        $sql = '
               SELECT budget_adjustment_date.day,
                      Max(budget_daily_adjustment.value) max_per_day
                FROM   budget_daily_adjustment
                       INNER JOIN budget_adjustment_date
                               ON budget_adjustment_date.id =
                                  budget_daily_adjustment.budget_date_id
                GROUP  BY budget_adjustment_date.day;   
        ';

        $statement = $connection->prepare($sql);
        $statement->execute();

        return $statement->fetchAllKeyValue();
    }
}
