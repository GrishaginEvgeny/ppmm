<?php

namespace App\Repository;

use App\Entity\Direction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Direction>
 */
class DirectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Direction::class);
    }

    public function countApplicationsByDirection(): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.name AS name, COUNT(es.student) AS total')
            ->join('d.events', 'e')
            ->join('e.students', 'es')
            ->groupBy('d.id')
            ->orderBy('total', 'DESC')
            ->indexBy('d', 'd.name')
            ->getQuery()
            ->getArrayResult();
    }

//    public function findOneBySomeField($value): ?Direction
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
