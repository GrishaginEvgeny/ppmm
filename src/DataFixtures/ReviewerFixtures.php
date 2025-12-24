<?php

namespace App\DataFixtures;

use App\Entity\Reviewer;
use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ReviewerFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $reviewersData = [
            [
                'fullName' => 'Смирнов Алексей Викторович',
                'login' => 'reviewer1',
            ],
            [
                'fullName' => 'Кузнецова Мария Сергеевна',
                'login' => 'reviewer2',
            ],
            [
                'fullName' => 'Смирнов Виктор Викторович',
                'login' => 'reviewer3',
            ],
            [
                'fullName' => 'Кузнецова Александра Сергеевна',
                'login' => 'reviewer4',
            ],
            [
                'fullName' => 'Смирнов Евгений Викторович',
                'login' => 'reviewer5',
            ],
        ];

        foreach ($reviewersData as $data) {
            $reviewer = new Reviewer();

            $reviewer
                ->setFullName($data['fullName'])
                ->setLogin($data['login']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $reviewer,
                'password'
            );

            $reviewer->setPassword($hashedPassword);

            $manager->persist($reviewer);
        }

        $manager->flush();
    }
}
