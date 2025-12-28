<?php

namespace App\DataFixtures;

use App\Factory\DirectionFactory;
use App\Factory\EventFactory;
use App\Factory\EventStudentFactory;
use App\Factory\ReviewerFactory;
use App\Factory\StudentFactory;
use Zenstruck\Foundry\Story;
use function Zenstruck\Foundry\faker;
use function Zenstruck\Foundry\Persistence\flush_after;

class MyStory extends Story
{

    public function build(): void
    {
        $dNames = [
            'Спортивное',
            'Научно-исследовательское',
            'Общественное',
            'Культурно-развлекательное',
            'Академическое',
        ];

        $students = StudentFactory::createMany(2885);
        $reviewers = ReviewerFactory::createMany(
            5
        );

        faker()->unique(true);

        $directions = DirectionFactory::createMany(
            count($reviewers),
            function (int $i) use ($reviewers, $dNames) {
                return [
                    'name' => $dNames[$i - 1],
                    'reviewer' => $reviewers[$i - 1],
                ];
            }
        );

        $events = EventFactory::createMany(
            75,
            function (int $i) use ($directions) {
                return [
                    'direction' => faker()->randomElement($directions)
                ];
            }
        );

        flush_after(function () use ($events, $students) {
            $used = [];

            for ($i = 0; $i < 1500; $i++) {
                do {
                    $event   = faker()->randomElement($events);
                    $student = faker()->randomElement($students);
                    $key = $event->getId().'-'.$student->getId();
                } while (isset($used[$key]));

                $used[$key] = true;

                EventStudentFactory::new([
                    'event' => $event,
                    'student' => $student,
                ])->create();
            }
        });
    }
}
