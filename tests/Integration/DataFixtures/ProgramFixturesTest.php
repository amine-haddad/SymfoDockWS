<?php

declare(strict_types=1);

namespace App\Tests\Integration\DataFixtures;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\ProgramFixtures;
use App\Entity\Category;
use App\Entity\Program;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Component\String\Slugger\SluggerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ProgramFixturesTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);


        // Commence une transaction pour isoler les tests
        $this->entityManager->beginTransaction();

        
    }

    protected function tearDown(): void
    {
        // Annule la transaction pour ne pas affecter la base de données
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

        $executor = new ORMExecutor($this->entityManager);
        $executor->execute($loader->getFixtures(), true);
    }

    public function testLoadPrograms(): void
    {
        // Vérifie que les programmes sont présents dans la base de données
        $programs = $this->entityManager->getRepository(Program::class)->findAll();
        self::assertCount(10, $programs);

        foreach ($programs as $program) {
            self::assertNotEmpty($program->getTitle());
            self::assertNotEmpty($program->getSlug());
            self::assertNotEmpty($program->getSynopsis());
            self::assertNotEmpty($program->getCategory());
            self::assertNotEmpty($program->getPoster());
            self::assertNotEmpty($program->getUpdatedAt());
            self::assertNotEmpty($program->getOwner());
        }
    }
}
