<?php

namespace App\Controller;

use App\Entity\Direction;
use App\Entity\EventStudent;
use App\Entity\Student;
use App\Enum\Status;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student')]
final class StudentController extends AbstractController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/new', name: 'app_student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $allStudents = $entityManager->getRepository(Student::class)->findBy(
                [],
                ['id' => 'DESC']
            );
            $maxIdStudent = reset($allStudents);
            $id = $maxIdStudent !== null ? $maxIdStudent->getId() + 1 : 1;
            $student->setLogin('student'.$id);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $student,
                'password'.$id
            );


            $student->setPassword($hashedPassword);
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[IsGranted('IS_STUDENT')]
    #[Route('/{id}', name: 'app_student_show', methods: ['GET'])]
    public function show(int $id, Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        /** @var Student $actualUser */
        $actualUser = $this->getUser();

        if ($actualUser->getId() !== $id)
        {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_student_show',  ['id' => $actualUser->getId()])
            );
        }

        $query = $entityManager->getRepository(EventStudent::class)
            ->createQueryBuilder('es')
            ->select('es', 'e', 'd')
            ->join('es.student', 's', 's.id = es.student_id')
            ->join('es.event', 'e', 'e.id = es.event_id')
            ->join('e.direction', 'd', 'e.direction_id = d.id')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        $pagination = $paginator->paginate(
            $query, /* Doctrine Query, QueryBuilder, or array */
            $request->query->getInt('page', 1), /* current page number */
            5 /* items per page */
        );

        $qb = $entityManager->getRepository(Direction::class)
            ->createQueryBuilder('d')
            ->leftJoin('d.events', 'e') // все события направления
            ->leftJoin('e.students', 'es', 'WITH', 'es.student = :studentId') // события студента
            ->select('d.name AS direction_name')
            ->addSelect('COALESCE(SUM(CASE WHEN es.status = :accepted THEN e.points ELSE 0 END), 0) AS total_points')
            ->setParameter('studentId', $id)
            ->setParameter('accepted', Status::ACCEPTED)
            ->groupBy('d.name')
            ->orderBy('d.name', 'DESC');

        $summary = $qb->getQuery()->getArrayResult();

        return $this->render('student/show.html.twig', [
            'student' => $actualUser,
            'events' => $pagination,
            'summary' => $summary,
        ]);
    }
}
