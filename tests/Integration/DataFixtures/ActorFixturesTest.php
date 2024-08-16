<?php

namespace App\Tests\Integration\DataFixtures;

use App\DataFixtures\ActorFixtures;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\ProgramFixtures;
use App\Entity\Actor;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActorFixturesTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->slugger = $container->get(SluggerInterface::class);

        // Début d'une transaction pour isoler les tests
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Annule la transaction pour revenir à l'état initial
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }

        $this->entityManager->close();
        parent::tearDown();
    }

    protected function loadFixtures(array $fixtureInstances): void
    {
        $loader = new Loader();
        foreach ($fixtureInstances as $fixture) {
            $loader->addFixture($fixture);
        }

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures(), true);
    }

    public function testLoadActors(): void
    {
        

        $actors = $this->entityManager->getRepository(Actor::class)->findAll();
        self::assertCount(10, $actors);

        foreach ($actors as $actor) {
            self::assertNotEmpty($actor->getName());
            self::assertNotEmpty($actor->getSlug());
            self::assertNotEmpty($actor->getPhoto());
            self::assertNotEmpty($actor->getUpdatedAt());
            self::assertNotEmpty($actor->getPrograms());
        }
    }

}
