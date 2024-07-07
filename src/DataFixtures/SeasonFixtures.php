<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class SeasonFixtures
 *
 * This class is responsible for loading and persisting Season data into the database.
 * It implements the DependentFixtureInterface to ensure that the Program data is loaded before this fixture.
 */
class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     *
     * This method loads Season data into the database.
     * It uses the Faker library to generate random data for the Season properties.
     *
     * @param ObjectManager $manager The Doctrine ObjectManager to manage the entity persistence
     */
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

    /**
     * {@inheritdoc}
     *
     * This method returns an array of fixture classes that this fixture depends on.
     * In this case, it depends on the ProgramFixtures class.
     *
     * @return array An array of fixture classes
     */
    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}
