<?php

namespace App\Repository;

use App\Entity\DailyGeneratedCost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DailyGeneratedCost|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyGeneratedCost|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyGeneratedCost[]    findAll()
 * @method DailyGeneratedCost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyGeneratedCostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyGeneratedCost::class);
    }

    // /**
    //  * @return DailyGeneratedCost[] Returns an array of DailyGeneratedCost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DailyGeneratedCost
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findMonthlyGeneratedCost($month)
    {
        $connection = $this->getEntityManager()
            ->getConnection();
        $sql = '
               SELECT Sum(daily_generated_cost.value) cost_generated 
            FROM   daily_generated_cost 
                   INNER JOIN budget_adjustment_date 
                           ON budget_adjustment_date.id = 
                              daily_generated_cost.budget_date_id 
            WHERE  Month(budget_adjustment_date.day) = ?
            GROUP  BY Month(budget_adjustment_date.day);
        ';

        $statement = $connection->prepare($sql);
        $statement->bindParam(1, $month);
        $statement->execute();

        return $statement->fetchOne();
    }

    public function findMaxBudgetAndGeneratedCostsPerDay()
    {
        $connection = $this->getEntityManager()
            ->getConnection();
        $sql = '
               SELECT daily_budget.day, 
               daily_budget.max_per_day, 
               Sum(daily_generated_cost.value) generated_cost 
        FROM   (SELECT budget_adjustment_date.id, 
                       budget_adjustment_date.day, 
                       Max(budget_daily_adjustment.value) max_per_day 
                FROM   budget_daily_adjustment 
                       INNER JOIN budget_adjustment_date 
                               ON budget_adjustment_date.id = 
                                  budget_daily_adjustment.budget_date_id 
                GROUP  BY budget_adjustment_date.day 
                ORDER  BY budget_adjustment_date.id ASC) daily_budget 
               INNER JOIN daily_generated_cost 
                       ON daily_generated_cost.budget_date_id = daily_budget.id 
        GROUP  BY daily_budget.day 
        ORDER  BY daily_budget.day ASC;  
        ';

        $statement = $connection->prepare($sql);
        $statement->execute();

        return $statement->fetchAllAssociative();
    }
}
