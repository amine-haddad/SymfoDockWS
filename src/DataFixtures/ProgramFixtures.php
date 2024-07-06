<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAMS = [
        [

            'title' => 'Walking Dead',
            'synopsis' => 'Des zombies envahissent la terre.',
            'category' => 'Horreur',
            'poster' => 'assets/images/the_walking_dead_saison_1.jpeg',
        ],
        [
            'title' => 'The Witcher',
            'synopsis' => "Geralt de Riv est un chasseur de monstres solitaire 
             qui s'efforce de trouver sa place dans un monde 
             où les gens se révèlent souvent plus cruels que les animaux.",
            'category' => "Fantasy",
            'poster' => '',
        ],
        [
            'title' => 'Fear the Walking dead',
            'synopsis' => "La série se déroule au tout début de l épidémie relatée dans la série-mère The Walking Dead et se passe dans la ville de Los Angeles, et non à Atlanta. Madison est conseillère dans un lycée de Los Angeles. Depuis la mort de son mari, elle élève seule ses deux enfants : Alicia, excellente élève qui découvre les premiers émois amoureux, et son grand frère Nick qui a quitté la fac et a sombré dans la drogue.",
            'category' => 'Horreur',
            'poster' => '',
        ],
        [
            'title' => 'Breaking Bad',
            'synopsis' => "Un professeur de chimie de lycée chez qui on a diagnostiqué un cancer du poumon inopérable se tourne vers la fabrication et la vente de méthamphétamine pour assurer l'avenir de sa famille.",
            'category' => 'Policier',
            'poster' => '',
        ],
        [
            'title' => 'Game of Thrones',
            'synopsis' => "Neuf nobles familles se battent pour le contrôle des terres mythiques de Westeros, tandis qu'un ancien ennemi revient après avoir été endormi pendant des milliers d'années.",
            'category' => 'Fantasy',
            'poster' => '',
        ],
        [
            'title' => 'Friends',
            'synopsis' => "Suit les vies personnelles et professionnelles de six amis d'une vingtaine et trentaine d'années vivant à Manhattan.",
            'category' => 'Comédie',
            'poster' => '',
        ],
    ];
    public function load(ObjectManager $manager): void
    {
        foreach (self::PROGRAMS as $key => $programData) {
            $program = new Program();
            $program->setTitle($programData['title']);
            $program->setSynopsis($programData['synopsis']);
            $program->setCategory($this->getReference($programData['category']));
            $program->setPoster($programData['poster']); // Ajout du poster à la programme

            $manager->persist($program);
            $this->addReference($programData['title'], $program);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}
