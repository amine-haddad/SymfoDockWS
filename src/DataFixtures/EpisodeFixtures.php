<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($programKey = 1; $programKey <= 10; $programKey++) {
            for ($seasonKey = 1; $seasonKey <= 5; $seasonKey++) {
                $seasonReference = 'season_' . $programKey . '_' . $seasonKey;

                //@var Season $season 
                $season = $this->getReference($seasonReference);

                for ($episodeKey = 1; $episodeKey <= rand(5, 12); $episodeKey++) {
                    $episode = new Episode();
                    $episode->setSeason($season);
                    $episode->setTitle($faker->sentence(3));
                    $episode->setNumber($episodeKey);
                    $episode->setSynopsis($faker->paragraph(3));
                    $manager->persist($episode);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}