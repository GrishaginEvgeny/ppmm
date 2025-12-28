<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private Connection $connection
    ) {
        parent::__construct($registry, Student::class);
    }

    public function getLadderByType(string $type, int $limit)
    {
        $sql = <<<SQL
WITH ranked_students AS (
    SELECT
        s.id          AS student_id,
        s.full_name   AS student_name,
        SUM(e.points) AS total_points,
        s.rating,
        s.course,
        RANK() OVER (
            ORDER BY
                SUM(e.points) DESC,
                s.rating DESC,
                s.course DESC
            ) AS rnk
    FROM event_student es
             JOIN event e      ON e.id = es.event_id
             JOIN direction d  ON d.id = e.direction_id
             JOIN student s    ON s.id = es.student_id
    WHERE es.status = 'одобрено'
      AND d.name = :direction_name
    GROUP BY s.id, s.full_name, s.rating, s.course
)
SELECT
    student_id,
    student_name,
    total_points,
    rating,
    course
FROM ranked_students
WHERE rating >= 80
ORDER BY rnk
LIMIT :limit
SQL;


        return $this->connection->fetchAllAssociative($sql, [
            'direction_name' => $type,
            'limit' => $limit
        ]);
    }

//    /**
//     * @return Student[] Returns an array of Student objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Student
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
