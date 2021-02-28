<?php

namespace App\Repository;

use App\Entity\CostDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CostDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CostDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CostDate[]    findAll()
 * @method CostDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CostDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CostDate::class);
    }

    // /**
    //  * @return CostDate[] Returns an array of CostDate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CostDate
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
