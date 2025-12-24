<?php

namespace App\Security;

use App\Entity\Student;
use App\Entity\Reviewer;
use App\Repository\StudentRepository;
use App\Repository\ReviewerRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class AppUserProvider implements UserProviderInterface
{
    public function __construct(
        private StudentRepository $studentRepository,
        private ReviewerRepository $reviewerRepository,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Сначала ищем студента
        $user = $this->studentRepository->findOneBy(['login' => $identifier]);

        if (!$user) {
            // Если не нашли студента, ищем ревьюера
            $user = $this->reviewerRepository->findOneBy(['login' => $identifier]);
        }

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with login "%s" not found.', $identifier));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Student && !$user instanceof Reviewer) {
            throw new \InvalidArgumentException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $class = get_class($user);
        $repository = $class === Student::class
            ? $this->studentRepository
            : $this->reviewerRepository;

        $refreshedUser = $repository->find($user->getId());

        if (!$refreshedUser) {
            throw new UserNotFoundException(sprintf('User with id %d not found', $user->getId()));
        }

        return $refreshedUser;
    }

    public function supportsClass(string $class): bool
    {
        return Student::class === $class || Reviewer::class === $class;
    }
}
