<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reviewer;
use App\Form\ReviewerType;
use App\Repository\ReviewerRepository;
use Doctrine\ORM\EntityManagerInterface;
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
                $this->urlGenerator->generate('app_student_show',  ['id' => $actualUser->getId()])
            );
        }

        $events = $entityManager->getRepository(Event::class)->findBy([
            'direction' => $actualUser->getDirection()
        ]);

        return $this->render('reviewer/show.html.twig', [
            'reviewer' => $actualUser,
            'events' => $events,
        ]);
    }
}
