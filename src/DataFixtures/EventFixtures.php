<?php

namespace App\DataFixtures;

use App\Entity\Direction;
use App\Entity\Event;
use App\Entity\EventStudent;
use App\Entity\Student;
use App\Enum\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        /** @var Direction[] $reviewer */
        $directions = $manager->getRepository(Direction::class)->findAll();

        $students = $manager->getRepository(Student::class)->findAll();

        $events = [
            [
                'name' => 'Соревнования по футболу',
                'level' => 'Всероссийские',
                'points' => 10,
                'status' => Status::ON_CHECK,
            ],
            [
                'name' => 'Соревнования по волейболу',
                'level' => 'Региональный',
                'points' => 5,
                'status' => Status::ON_CHECK,
            ],
            [
                'name' => 'Соревнования по баскетболу',
                'level' => 'Межвузовский',
                'points' => 3,
                'status' => Status::ON_CHECK,
            ],
            [
                'name' => 'Соревнования по дзюдо',
                'level' => 'Вузовский',
                'points' => 1,
                'status' => Status::ON_CHECK,
            ],
            [
                'name' => 'Соревнования по шахматам',
                'level' => 'Факультетские',
                'points' => 0.5,
                'status' => Status::ON_CHECK,
            ],
        ];

        foreach ($events as $key => $event) {
            $newEvent = new Event();
            $newEvent
                ->setName($event['name'])
                ->setLevel($event['level'])
                ->setDirection($directions[$key])
                ->setPoints($event['points']);

            $eventStudent = new EventStudent();

            $eventStudent->setEvent($newEvent);
            $eventStudent->setStudent($students[random_int(1, count($students)) - 1]);
            $eventStudent->setStatus(Status::ON_CHECK);
            $eventStudent->setLink('link' . $key);

            $manager->persist($newEvent);
            $manager->persist($eventStudent);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReviewerFixtures::class,
            StudentFixtures::class,
        ];
    }
}
