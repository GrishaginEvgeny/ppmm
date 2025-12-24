<?php

namespace App\Controller;

use App\Entity\Reviewer;
use App\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    )
    {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $actualUser = $this->getUser();

        if ($actualUser !== null) {
            return new RedirectResponse(
                $this->urlGenerator->generate('app_home')
            );
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/', name: 'app_home')]
    public function redirectOnSuccess(): Response
    {
        $user = $this->getUser();

        return match (true) {
            $user instanceof Student => $this->redirectToRoute('app_student_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER),
            $user instanceof Reviewer => $this->redirectToRoute('app_reviewer_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER),
            default => $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER),
        };
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
