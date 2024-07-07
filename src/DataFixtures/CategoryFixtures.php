<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
//Tout d'abord nous ajoutons la classe Factory de FakerPhp
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    const CATEGORIES = [
        'Action',
        'Aventure',
        'Comédie',
        'Drame',
        'Fantasy',
        'Historique',
        'Horreur',
        'Musical',
        'Policier',
        'Romance',
        'Science-Fiction',
        'Thriller',
        'Western',
        
    ];

    public function load(ObjectManager $manager): void
    {
        //Puis ici nous demandons à la Factory de nous fournir un Faker
        $faker = Factory::create();
        for ($i=1; $i <= 15 ; $i++) { 
       
            $category = new Category();
            $category->setName($faker->word());

            $manager->persist($category);
            $this->addReference( 'category_'.$i, $category);
        }

        $manager->flush();
    }
}
