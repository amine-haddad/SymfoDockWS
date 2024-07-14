<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // The goal is to create 10 programs that will belong to a random category
        for ($i = 1; $i <= 10; $i++) {
            $program = new Program();
            $program->setTitle($faker->words($faker->numberBetween(1, 3), true));
            $slugTitle = $this->slugger->slug($program->getTitle())->lower();
            $program->setSlug($slugTitle);
            $program->setSynopsis($faker->paragraphs(2, true));
            $program->setCategory($this->getReference('category_' . $faker->numberBetween(1, 15)));
            $program->setPoster($faker->imageUrl(640, 480, 'cinema'));

            // Assign a random owner from the users
            $userReference = $faker->randomElement(['user_contributor', 'user_admin']);
            $program->setOwner($this->getReference($userReference));

            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}
