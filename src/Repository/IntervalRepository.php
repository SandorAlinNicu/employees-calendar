<?php

namespace App\Repository;

use App\Entity\Interval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Interval|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interval|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interval[]    findAll()
 * @method Interval[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntervalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interval::class);
    }

    // /**
    //  * @return Interval[] Returns an array of Interval objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Interval
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
