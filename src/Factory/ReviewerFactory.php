<?php

namespace App\Factory;

use App\Entity\Reviewer;
use App\Entity\Student;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Reviewer>
 */
final class ReviewerFactory extends PersistentProxyObjectFactory
{
    private static int $loginCounter = 1;

    private static ?string $hashedPassword = null;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public static function class(): string
    {
        return Reviewer::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'fullName' => self::faker()->name(),
            'login' => 'reviewer'.self::$loginCounter++,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this->afterInstantiate(function(Reviewer $student) {
            // вычисляем хэш один раз для всех студентов
            if (self::$hashedPassword === null) {
                self::$hashedPassword = $this->passwordHasher->hashPassword($student, 'password');
            }
            $student->setPassword(self::$hashedPassword);
        });
    }
}
