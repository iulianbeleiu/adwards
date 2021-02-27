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
}
