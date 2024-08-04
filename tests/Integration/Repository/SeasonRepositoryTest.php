<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Season;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeasonRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $seasonRepository;
    private $programRepository;

    /**
     * Initialisation avant chaque test.
     * Démarre le noyau Symfony et récupère les services nécessaires.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->seasonRepository = $this->entityManager->getRepository(Season::class);
        $this->programRepository = $this->entityManager->getRepository(Program::class);
    }

    /**
     * Nettoyage après chaque test.
     * Ferme l'EntityManager et le réinitialise.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * Crée une saison en associant un programme existant avec un numéro de saison spécifique.
     * 
     * @param int $number Le numéro de la saison à créer.
     * @return Season La saison créée.
     * @throws \RuntimeException Si aucun programme n'est trouvé dans la base de données.
     */
    private function createSeason(int $number): Season
    {
        // Assure-toi qu'il existe au moins un programme dans la base de données
        $program = $this->programRepository->findOneBy([]);

        if (!$program) {
            throw new \RuntimeException('No program found in the database. Please seed the database with programs.');
        }

        // Crée une nouvelle instance de la saison
        $season = new Season();
        $season->setNumber($number);
        $season->setYear(2024);
        $season->setDescription("Season $number description");
        $season->setProgram($program);

        // Persiste et enregistre la saison dans la base de données
        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $season;
    }

    /**
     * Teste la création d'une saison.
     */
    public function testCreateSeason(): void
    {
        // Crée une saison avec le numéro 1
        $season = $this->createSeason(1);

        // Récupère la saison sauvegardée depuis la base de données
        $savedSeason = $this->seasonRepository->find($season->getId());

        // Vérifie que la saison a été correctement sauvegardée
        $this->assertNotNull($savedSeason, 'Season should be saved.');
        $this->assertSame($season->getNumber(), $savedSeason->getNumber(), 'Season number should match.');
        $this->assertSame($season->getYear(), $savedSeason->getYear(), 'Season year should match.');
        $this->assertSame($season->getDescription(), $savedSeason->getDescription(), 'Season description should match.');
        $this->assertSame($season->getProgram()->getId(), $savedSeason->getProgram()->getId(), 'Season program should match.');
    }

    /**
     * Teste la suppression d'une saison.
     */
    public function testDeleteSeason(): void
    {
        // Crée une saison avec le numéro 2
        $season = $this->createSeason(2);

        // Récupère l'identifiant de la saison
        $seasonId = $season->getId();
        $this->assertNotNull($seasonId, 'Season ID should not be null after persisting.');

        // Supprime la saison de la base de données
        $this->entityManager->remove($season);
        $this->entityManager->flush();

        // Vérifie que la saison a été correctement supprimée
        $deletedSeason = $this->seasonRepository->find($seasonId);
        $this->assertNull($deletedSeason, 'Season should be null after deletion.');
    }

    /**
     * Teste la récupération de toutes les saisons.
     */
    public function testFindAllSeasons(): void
    {
        // Compte le nombre de saisons avant d'en ajouter de nouvelles
        $initialCount = $this->seasonRepository->count([]);

        // Crée des saisons avec des numéros distincts
        $this->createSeason(3);
        $this->createSeason(4);

        // Récupère toutes les saisons depuis la base de données
        $seasons = $this->seasonRepository->findAll();

        // Vérifie que le nombre de saisons dans la base de données est correct
        $this->assertCount($initialCount + 2, $seasons, 'The number of seasons should match the count after adding.');
    }
}
