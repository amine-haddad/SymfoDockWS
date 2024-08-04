<?php

// src/Tests/Repository/ProgramRepositoryTest.php

namespace App\Tests\Repository;

use App\Entity\Program;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProgramRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private $programRepository = null;
    private $categoryRepository = null;


    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->programRepository = $this->entityManager->getRepository(Program::class);
        $this->categoryRepository = self::getContainer()->get(CategoryRepository::class);
    }

    public function testFindLikeName(): void
    {
        // Récupérer tous les programmes
        $allPrograms = $this->programRepository->findAll();
        
        // Récupérer les titres des programmes
        $titles = array_map(fn($program) => $program->getTitle(), $allPrograms);

        // Vérifier qu'il y a au moins un titre
        $this->assertNotEmpty($titles, 'No titles found in the database.');

        // Sélectionner aléatoirement un titre pour le test
        $randomTitle = $titles[array_rand($titles)];

        // Effectuer le test avec un titre aléatoire
        $result = $this->programRepository->findLikeName($randomTitle);
        $this->assertGreaterThan(0, count($result), "No results found for the random title: $randomTitle");

        // Récupérer les noms des acteurs associés aux programmes
        $actorNames = [];
        foreach ($allPrograms as $program) {
            foreach ($program->getActors() as $actor) {
                $actorNames[] = $actor->getName();
            }
        }

        // Vérifier qu'il y a au moins un nom d'acteur
        $this->assertNotEmpty($actorNames, 'No actor names found in the database.');

        // Sélectionner aléatoirement un nom d'acteur pour le test
        $randomActorName = $actorNames[array_rand($actorNames)];

        // Effectuer le test avec un nom d'acteur aléatoire
        $result = $this->programRepository->findLikeName($randomActorName);
        $this->assertGreaterThan(0, count($result), "No results found for the random actor name: $randomActorName");

        // Test avec un mot clé qui devrait retourner plusieurs résultats (ajuste le mot clé selon tes données)
        //$keyword = 'voluptas'; // Exemple de mot clé
        //$result = $this->programRepository->findLikeName($keyword);
        //$this->assertGreaterThan(1, count($result), "Expected more than one result for the keyword: $keyword");
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
