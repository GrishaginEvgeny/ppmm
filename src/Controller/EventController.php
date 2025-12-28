<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventStudent;
use App\Entity\Reviewer;
use App\Entity\Student;
use App\Enum\Status;
use App\Form\EventType;
use App\Form\LinkType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/event')]
final class EventController extends AbstractController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    )
    {
    }

    #[IsGranted('IS_STUDENT')]
    #[Route('', name: 'app_event_index', methods: ['get'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $query = $entityManager->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* Doctrine Query, QueryBuilder, or array */
            $request->query->getInt('page', 1), /* current page number */
            10 /* items per page */
        );

        return $this->render('event/index.html.twig', [
            'events' => $pagination,
            'user' => $this->getUser(),
            'eventStudents' => $entityManager->getRepository(EventStudent::class)->findAll(),
        ]);
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[IsGranted(
        new Expression("is_granted('IS_STUDENT') or is_granted('IS_REVIEWER')")
    )]
    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);

        if ($event === null) {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_event_index')
            );
        }

        /** @var Reviewer|Student $actualUser */
        $actualUser = $this->getUser();

        if ($actualUser instanceof Student)
        {
            $eventStudent = $entityManager->getRepository(EventStudent::class)
                ->findOneBy([
                    'event' => $event,
                    'student' => $actualUser,
                ]);

            if (null === $eventStudent) {
                return new RedirectResponse(
                    $this->urlGenerator->generate('app_student_show', ['id' => $actualUser->getId()])
                );
            }
        }

        if ($actualUser instanceof Reviewer && $actualUser->getDirection() !== $event->getDirection())
        {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_event_index',  ['id' => $actualUser->getId()])
            );
        }

        $query = $entityManager->getRepository(EventStudent::class)
            ->createQueryBuilder('es')
            ->select('es', 's')
            ->join('es.student', 's', 's.id = es.student_id')
            ->join('es.event', 'e', 'e.id = es.event_id')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        $pagination = $paginator->paginate(
            $query, /* Doctrine Query, QueryBuilder, or array */
            $request->query->getInt('page', 1), /* current page number */
            10 /* items per page */
        );

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'students' => $pagination,
        ]);
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        /** @var Reviewer $user */
        $user = $this->getUser();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventStudent = $entityManager->getRepository(EventStudent::class)->findOneBy([
                'event' => $event,
                'student' => $user,
            ]);
            $eventStudent->setStatus(Status::ON_CHECK);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/{id}/decline/{studentId}', name: 'app_event_status_decline', methods: ['GET'])]
    public function decline(int $id, int $studentId, EntityManagerInterface $entityManager): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        $student = $entityManager->getRepository(Student::class)->find($studentId);

        if ($event === null || $student === null) {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_event_index')
            );
        }

        /**
         * @var EventStudent $evSt
         */
        $evSt = $entityManager->getRepository(EventStudent::class)->findOneBy([
            'event' => $event,
            'student' => $student,
        ]);

        /** @var Reviewer $actualUser */
        $actualUser = $this->getUser();

        if ($actualUser->getDirection() !== $event->getDirection())
        {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_event_index',  ['id' => $actualUser->getId()])
            );
        }

        $evSt->setStatus(Status::DECLINED);
        $entityManager->persist($evSt);
        $entityManager->flush();

        return new RedirectResponse(
            $this->urlGenerator->generate('app_event_show',  ['id' => $id])
        );
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/{id}/accept/{studentId}', name: 'app_event_status_accept', methods: ['GET'])]
    public function accept(int $id, int $studentId, EntityManagerInterface $entityManager): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        $student = $entityManager->getRepository(Student::class)->find($studentId);

        if ($event === null || $student === null) {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_event_show',  ['id' => $id])
            );
        }

        /**
         * @var EventStudent $evSt
         */
        $evSt = $entityManager->getRepository(EventStudent::class)->findOneBy([
            'event' => $event,
            'student' => $student,
        ]);


        /** @var Reviewer $actualUser */
        $actualUser = $this->getUser();

        if ($actualUser->getDirection() !== $event->getDirection())
        {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_event_index',  ['id' => $actualUser->getId()])
            );
        }

        $evSt->setStatus(Status::ACCEPTED);
        $entityManager->persist($evSt);
        $entityManager->flush();

        return new RedirectResponse(
            $this->urlGenerator->generate('app_event_show',  ['id' => $id])
        );
    }

    #[IsGranted('IS_STUDENT')]
    #[Route('/{id}/link', name: 'app_event_link', methods: ['GET', 'POST'])]
    public function link(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if ($event === null) {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_event_index')
            );
        }
        /** @var Student $user */
        $user = $this->getUser();
        $form = $this->createForm(LinkType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var string $link */
            $link = $data['link'];

            $eventS = $entityManager->getRepository(EventStudent::class)->findOneBy([
                'event' => $event,
                'student' => $user,
            ]) ?? new EventStudent();

            $eventS->setEvent($event);
            $eventS->setStudent($user);
            $eventS->setStatus(Status::ON_CHECK);
            $eventS->setLink($link);
            $entityManager->persist($eventS);
            $entityManager->flush();

            return new RedirectResponse(
                $this->urlGenerator->generate('app_home')
            );
        }

        return $this->render('event/link.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

}
