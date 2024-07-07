<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class EpisodeFixtures
 *
 * This class is responsible for loading episode data into the database.
 * It implements the DependentFixtureInterface to ensure that the Season data is loaded before this fixture.
 */
class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     *
     * This method loads episode data into the database.
     * It uses Faker to generate random data for episode titles and synopses.
     *
     * @param ObjectManager $manager The Doctrine object manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Loop through 10 programs
        for ($programKey = 1; $programKey <= 10; $programKey++) {
            // Loop through 5 seasons per program
            for ($seasonKey = 1; $seasonKey <= 5; $seasonKey++) {
                $seasonReference = 'season_' . $programKey . '_' . $seasonKey;

                // Get the referenced season
                //@var Season $season
                $season = $this->getReference($seasonReference);

                // Generate a random number of episodes between 5 and 12
                for ($episodeKey = 1; $episodeKey <= rand(5, 12); $episodeKey++) {
                    $episode = new Episode();
                    $episode->setSeason($season);
                    $episode->setTitle($faker->sentence(3));
                    $episode->setNumber($episodeKey);
                    $episode->setSynopsis($faker->paragraph(3));

                    // Persist the episode to the database
                    $manager->persist($episode);
                }
            }
        }

        // Flush the changes to the database
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * This method returns an array of fixture classes that this fixture depends on.
     * In this case, it depends on the SeasonFixtures.
     *
     * @return array An array of fixture classes
     */
    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}
