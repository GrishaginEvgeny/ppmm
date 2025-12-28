<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reviewer;
use App\Form\ReviewerType;
use App\Repository\ReviewerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reviewer')]
final class ReviewerController extends AbstractController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    )
    {
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/{id}', name: 'app_reviewer_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var Reviewer $actualUser */
        $actualUser = $this->getUser();

        if ($actualUser->getId() !== $id)
        {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_reviewer_show',  ['id' => $actualUser->getId()])
            );
        }

        $events = $entityManager->getRepository(Event::class)->findBy([
            'direction' => $actualUser->getDirection(),
        ]);

        return $this->render('reviewer/show.html.twig', [
            'reviewer' => $actualUser,
            'events' => $events,
        ]);
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/{id}/events', name: 'app_reviewer_events', methods: ['GET'])]
    public function events(int $id, Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        /** @var Reviewer $actualUser */
        $actualUser = $this->getUser();

        if ($actualUser->getId() !== $id)
        {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_reviewer_show',  ['id' => $actualUser->getId()])
            );
        }

        $query = $entityManager->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->andWhere('e.direction = :dir')
            ->setParameter('dir', $actualUser->getDirection())
            ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* Doctrine Query, QueryBuilder, or array */
            $request->query->getInt('page', 1), /* current page number */
            10 /* items per page */
        );

        return $this->render('reviewer/events.html.twig', [
            'reviewer' => $actualUser,
            'pagination' => $pagination,
        ]);
    }

    #[IsGranted('IS_REVIEWER')]
    #[Route('/{id}/students', name: 'app_reviewer_students', methods: ['GET'])]
    public function students(int $id, Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        /** @var Reviewer $actualUser */
        $actualUser = $this->getUser();

        if ($actualUser->getId() !== $id)
        {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_reviewer_show',  ['id' => $actualUser->getId()])
            );
        }

        $query = $entityManager->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->andWhere('direction', ':dir')
            ->setParameter('dir', $actualUser->getDirection())
            ->getQuery();

        $pagination = $paginator->paginate(
            $query, /* Doctrine Query, QueryBuilder, or array */
            $request->query->getInt('page', 1), /* current page number */
            10 /* items per page */
        );

        return $this->render('reviewer/events.html.twig', [
            'reviewer' => $actualUser,
            'events' => $pagination,
        ]);
    }
}
