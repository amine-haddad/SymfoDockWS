<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    const SEASONS = [
        [
            'number' => 1,
            'year' => 2010,
            'description' => "Le temps d'une saison",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 2,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 3,
            'year' => 2010,
            'description' => "Le temps d'une saison",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 4,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 5,
            'year' => 2010,
            'description' => "Le temps d'une saison",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 6,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 7,
            'year' => 2010,
            'description' => "Le temps d'une saison",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 8,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 9,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 10,
            'year' => 2010,
            'description' => "Le temps d'une saison",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 11,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'Walking Dead',
        ],
        [
            'number' => 1,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'The Witcher',
        ],
        [
            'number' => 2,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'The Witcher',
        ],
        [
            'number' => 3,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'The Witcher',
        ],
        [
            'number' => 2,
            'year' => 2012,
            'description' => "Le temps de 2 saisons",
            'program' => 'Game of Thrones',
        ],
        [
            'number' => 1,
            'year' => 2012,
            'description' => "Le temps de 1 saison",
            'program' => 'Friends',
        ],
        [
            'number' => 4,
            'year' => 2012,
            'description' => "Le temps de 3 saisons",
            'program' => 'Game of Thrones',
        ],
        [   
            'number' => 2,
            'year' => 2011,
            'description' => "Le temps de 2 saisons",
            'program' => 'The Witcher',
        ],
        [
            'number' => 3,
            'year' => 2012,
            'description' => "Le temps de 3 saisons",
            'program' => 'Game of Thrones',
        ],
        [
            'number' => 4,
            'year' => 2013,
            'description' => "Le temps de 4 saisons",
            'program' => 'Friends',
        ],
        [
            'number' => 5,
            'year' => 2014,
            'description' => "Le temps de 5 saisons",
            'program' => 'Breaking Bad',
        ],
        [
            'number' => 6,
            'year' => 2015,
            'description' => "Le temps de 6 saisons",
            'program' => 'Breaking Bad',
        ],
        [
            'number' => 7,
            'year' => 2016,
            'description' => "Le temps de 7 saisons",
            'program' => 'Breaking Bad',
        ],
        [
            'number' => 8,
            'year' => 2017,
            'description' => "Le temps de 8 saisons",
            'program' => 'Breaking Bad',
        ],
        [
            'number' => 9,
            'year' => 2018,
            'description' => "Le temps de 9 saisons",
            'program' => 'Breaking Bad',
        ],
        [
            'number' => 10,
            'year' => 2019,
            'description' => "Le temps de 10 saisons",
            'program' => 'Breaking Bad',
        ],
        [
            'number' => 11,
            'year' => 2021,
            'description' => "Le temps de 11 saisons",
            'program' => 'Breaking Bad',
        ],

    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::SEASONS as $key => $seasonData) {
            $season = new Season();
            $season->setNumber($seasonData['number']);
            $season->setYear($seasonData['year']);
            $season->setDescription($seasonData['description']);
            $season->setProgram($this->getReference($seasonData['program']));
            $manager->persist($season);
            
            // Définir la référence avec une clé unique
            $this->addReference('season_' .$key , $season);
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
