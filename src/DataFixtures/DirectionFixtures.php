<?php

namespace App\DataFixtures;

use App\Entity\Direction;
use App\Entity\Reviewer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DirectionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var Reviewer[] $reviewer */
        $reviewers = $manager->getRepository(Reviewer::class)->findAll();

        $directions = [
            'Спортивное',
            'Научно-исследовательское',
            'Общественное',
            'Культурно-развлекательное',
            'Академическое',
        ];

        foreach ($reviewers as $key => $reviewer) {
            $direction = new Direction();

            $direction->setName($directions[$key]);
            $direction->setReviewer($reviewer);

            $manager->persist($direction);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReviewerFixtures::class,
        ];
    }
}
