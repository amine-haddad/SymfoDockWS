<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;
    private $faker;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->faker = Factory::create('fr_FR');
    }
  

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $actor = new Actor();
            $actor->setName($this->faker->name());
            $slugName = $this->slugger->slug($actor->getName());
            $actor->setSlug($slugName);
            $manager->persist($actor);
    
            // Ajout de l'acteur à un ou plusieurs programmes
            $programCount = random_int(1, 10);
            for ($j = 1; $j <= $programCount; $j++) {
                
                $program = $this->getReference('program_' . random_int(1, 10));
                $program->addActor($actor);
                $manager->persist($program);
            }
            $this->addReference('actor_' . $i, $actor);
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
