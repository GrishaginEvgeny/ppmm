<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StudentFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $studentsData = [
            [
                'id' => 111222,
                'fullName' => 'Иванов Иван Иванович',
                'login' => 'student1',
                'studyGroup' => 'ПМ-24-1',
                'institute' => 'ИКН',
                'rating' => 85,
            ],
            [
                'id' => 111223,
                'fullName' => 'Сидорова Анна Сергеевна',
                'login' => 'student3',
                'studyGroup' => 'УК-24-1',
                'institute' => 'ИКН',
                'rating' => 94,
            ],
            [
                'id' => 111224,
                'fullName' => 'Петров Пётр Петрович',
                'login' => 'student2',
                'studyGroup' => 'МА-24-1',
                'institute' => 'ФЭИ',
                'rating' => 72,
            ],
        ];

        foreach ($studentsData as $data) {
            $student = new Student();

            $student
                ->setId($data['id'])
                ->setFullName($data['fullName'])
                ->setLogin($data['login'])
                ->setStudyGroup($data['studyGroup'])
                ->setInstitute($data['institute'])
                ->setRating($data['rating']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $student,
                'password'
            );

            $student->setPassword($hashedPassword);

            $manager->persist($student);
        }

        $manager->flush();
    }
}
