<?php

namespace App\DataFixtures;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class ProgramFixtures
 *
 * This class is responsible for creating and persisting Program entities in the database.
 * It uses Doctrine's ObjectManager to interact with the database and Faker to generate random data.
 *
 * @package App\DataFixtures
 */
class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // The goal is to create 10 programs that will belong to a random category
        for ($i = 1; $i <= 10; $i++) {
            $program = new Program();
            $program->setTitle($faker->words($faker->numberBetween(1, 3), true));
            $program->setSynopsis($faker->paragraphs(2, true));
            $program->setCategory($this->getReference('category_' . $faker->numberBetween(1, 15)));
            // $program->setPoster($programData['poster']); // Adding poster to the program

            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
