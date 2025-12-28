<?php

namespace App\Controller;

use App\Entity\Direction;
use App\Entity\Student;
use App\Enum\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[IsGranted('IS_REVIEWER')]
    #[Route('/dashboard/participation', name: 'app_dashboard_participation')]
    public function participation(EntityManagerInterface $entityManager): Response
    {
        $countsByDirection = $entityManager->getRepository(Direction::class)
            ->countApplicationsByDirection();

        $labels = array_keys($countsByDirection);
        $values = array_values(array_map(function (array $direction): int {
            return $direction['total'];
        }, $countsByDirection));

        return $this->render('dashboard/participation.html.twig', [
            'name' => 'Количество заявлений по направлениям',
            'labels' => json_encode($labels),
            'values' => json_encode($values),
        ]);
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/dashboard/student', name: 'app_dashboard_student')]
    public function student(EntityManagerInterface $entityManager): Response
    {
        $qb = $entityManager->createQueryBuilder();
        $qb->select('s.id, s.rating, SUM(e.points) as totalPoints')
            ->from(Student::class, 's')
            ->join('s.events', 'es')
            ->join('es.event', 'e')
            ->groupBy('s.id, s.rating')
            ->andWhere('es.status = :status')
            ->setParameter('status', Status::ACCEPTED->value)
        ;

        $studentsData = $qb->getQuery()->getArrayResult();

        $points = array_map(function (array $student): array {
            return ["x" => $student["rating"], "y" => (float) $student["totalPoints"]];
        },
            $studentsData
        );

        $qb2 = $entityManager->createQueryBuilder()
            ->from(Student::class, 's')
            ->select('s.institute AS institute, COUNT(s.id) AS count')
            ->groupBy('s.institute')
            ->orderBy('count', 'DESC');

        $result = $qb2->getQuery()->getArrayResult();

        $labels = array_column($result, 'institute');
        $values = array_column($result, 'count');

        return $this->render('dashboard/students.html.twig', [
            'points' => json_encode($points),
            'points_name' => 'Отношение академ. рейтинга к активист. рейтингу',
            'x_name' => 'Академический рейтинг',
            'y_name' => 'Сумма баллов за участие',
            'labels' => json_encode($labels),
            'values' => json_encode($values),
            'pie_name' => 'Распределение студентов по институтам',
        ]);
    }
}
