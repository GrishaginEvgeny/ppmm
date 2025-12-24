<?php

namespace App\Security;

use App\Entity\Reviewer;
use App\Entity\Student;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserTypeVoter extends Voter
{

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['IS_STUDENT', 'IS_REVIEWER']);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            'IS_STUDENT' => $user instanceof Student,
            'IS_REVIEWER' => $user instanceof Reviewer,
            default => false,
        };
    }
}
