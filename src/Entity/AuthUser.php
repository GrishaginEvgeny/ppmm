<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    function __construct(private AbstractUser $user)
    {
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getId();
    }

    /**
     * @return AbstractUser
     */
    public function getUser(): AbstractUser
    {
        return $this->user;
    }

    /**
     * @param AbstractUser $user
     */
    public function setUser(AbstractUser $user): void
    {
        $this->user = $user;
    }

    public function getPassword(): ?string
    {
        return $this->user->getId();
    }
}
