<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
// Tout d'abord nous ajoutons la classe Factory de FakerPhp
use Faker\Factory;

/**
 * Class CategoryFixtures
 * @package App\DataFixtures
 *
 * This class is responsible for loading fake data into the Category entity.
 */
class CategoryFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        // Puis ici nous demandons à la Factory de nous fournir un Faker
        $faker = Factory::create();

        // Loop to create 15 categories
        for ($i = 1; $i <= 15; $i++) {
            // Create a new Category entity
            $category = new Category();

            // Set the name of the category using a random word from Faker
            $category->setName($faker->word());

            // Persist the category to the database
            $manager->persist($category);

            // Add a reference to the category for future use
            $this->addReference('category_' . $i, $category);
        }

        // Flush the changes to the database
        $manager->flush();
    }
}
