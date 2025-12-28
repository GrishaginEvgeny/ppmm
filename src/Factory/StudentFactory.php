<?php

namespace App\Factory;

use App\Entity\Student;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Student>
 */
final class StudentFactory extends PersistentProxyObjectFactory
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
        return Student::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $letters = ['А','Б','В','Г','Д','Е','Ж','З','И','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Э','Ю','Я'];

        $prefix = $letters[array_rand($letters)] . $letters[array_rand($letters)];
        $year   = random_int(21, 25);

        return [
            'id' => self::faker()->unique()->randomNumber(6, true),
            'fullName' => self::faker()->name(),
            'institute' => self::faker()->randomElement(
                ['ИКН', 'МИ', 'ФЭИ', 'ИСНЭП', 'ИМИТ', 'ИСА']
            ),
            'login' => 'student'.self::$loginCounter++,
            'rating' => self::faker()->numberBetween(0, 100),
            'studyGroup' => $prefix.'-'.$year,
            'course' => self::faker()->numberBetween(1,5),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this->afterInstantiate(function(Student $student) {
            // вычисляем хэш один раз для всех студентов
            if (self::$hashedPassword === null) {
                self::$hashedPassword = $this->passwordHasher->hashPassword($student, 'password');
            }
            $student->setPassword(self::$hashedPassword);
        });
    }
}
