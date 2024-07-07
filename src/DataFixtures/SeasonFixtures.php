<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($programKey = 1; $programKey <= 10; $programKey++) {
            $programRef = $this->getReference('program_' . $programKey);
            $startYear = $faker->numberBetween(1990, 2015); // Random start year between 1990 and 2015

            for ($seasonKey = 1; $seasonKey <= 5; $seasonKey++) {
                $season = new Season();
                $season->setProgram($programRef);
                $season->setNumber($seasonKey);
                $season->setYear($startYear + $seasonKey - 1); // Increment the year for each season
                $season->setDescription($faker->paragraph());
                $manager->persist($season);

                $this->addReference('season_' . $programKey . '_' . $seasonKey, $season);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}

