<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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
        foreach (self::CATEGORIES as $key => $categoryName) { 
            # code...
            $category = new Category();
            $category->setName($categoryName);

            $manager->persist($category);
            $this->addReference( $categoryName, $category);
        }

        $manager->flush();
    }
}
