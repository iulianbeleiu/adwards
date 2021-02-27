<?php

namespace App\Repository;

use App\Entity\BudgetAdjustmentDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BudgetAdjustmentDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method BudgetAdjustmentDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method BudgetAdjustmentDate[]    findAll()
 * @method BudgetAdjustmentDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetAdjustmentDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetAdjustmentDate::class);
    }

    // /**
    //  * @return BudgetAdjustmentDate[] Returns an array of BudgetAdjustmentDate objects
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
    public function findOneBySomeField($value): ?BudgetAdjustmentDate
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
