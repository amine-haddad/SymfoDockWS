<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Episode;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    const EPISODES = [
        ['title' => 'Days Gone Bye', 'synopsis' => "Le shérif adjoint Rick Grimes se réveille d'un coma et cherche sa famille dans un monde ravagé par les morts-vivants.", 'number' => 1, 'season' => 0],
        ['title' => 'Guts', 'synopsis' => "In Atlanta, Rick is rescued by a group of survivors, but they soon find themselves trapped inside a department store surrounded by walkers.", 'number' => 2, 'season' => 0],
        ['title' => 'Tell It to the Frogs', 'synopsis' => "Rick is reunited with Lori and Carl but soon decides - along with some of the other survivors - to return to the rooftop and rescue Merle. Meanwhile, tensions run high between the other survivors at the camp.", 'number' => 3, 'season' => 0],
        ['title' => ' Vatos', 'synopsis' => 'Rick, Glenn, Daryl and T-Dog come across a group of seemingly hostile survivors whilst searching for Merle. Back at camp, Jim begins behaving erratically.', 'number' => 4, 'season' => 0],
        ['title' => 'What Lies Ahead', 'synopsis' => "The group's plan to head for Fort Benning is put on hold when Sophia goes missing.", 'number' => 1, 'season' => 1],
        ['title' => "The End's Beginning", 'synopsis' => "Hostile townsfolk and a cunning mage greet Geralt in the town of Blaviken. Ciri finds her royal world upended when Nilfgaard sets its sights on Cintra.", 'number' => 1, 'season' =>   11],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::EPISODES as $episodeData) {
            $seasonId = $episodeData['season']; // ID de la saison à utiliser
            $seasonReference = $this->getReference('season_' . $seasonId); // Récupérer la saison par son ID
            $episode = new Episode();
            $episode->setTitle($episodeData['title']);
            $episode->setNumber($episodeData['number']);
            $episode->setSynopsis($episodeData['synopsis']);
            $episode->setSeason($seasonReference);
            $manager->persist($episode);
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
