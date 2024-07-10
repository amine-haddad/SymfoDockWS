<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $actor = new Actor();
            $actor->setName($this->faker->name());

            // Assign this actor to a random number of programs
            $programCount = random_int(1, 10); // Each actor will be associated with between 1 and 10 programs
            $assignedPrograms = [];
            for ($j = 1; $j < $programCount; $j++) {
                do {
                    $programReference = 'program_' . random_int(1, 10);
                } while (in_array($programReference, $assignedPrograms));
                $assignedPrograms[] = $programReference;
                $program = $this->getReference($programReference);
                $actor->addProgram($program);
            }

            $manager->persist($actor);
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
