<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function show(int $id): Response
    {
        /** @var Student $actualUser */
        $actualUser = $this->getUser();

        if ($actualUser->getId() !== $id)
        {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_student_show',  ['id' => $actualUser->getId()])
            );
        }

        $events = $actualUser->getEvents();

        return $this->render('student/show.html.twig', [
            'student' => $actualUser,
            'events' => $events,
        ]);
    }
}
