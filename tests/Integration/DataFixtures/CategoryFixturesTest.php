<?php

declare(strict_types=1);

namespace App\Tests\Integration\DataFixtures;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixturesTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    /**
     * Méthode exécutée avant chaque test pour initialiser les composants nécessaires.
     */
    protected function setUp(): void
    {
        // Démarre le noyau Symfony
        self::bootKernel();

        // Récupère le conteneur de services
        $container = self::getContainer();

        // Récupère l'EntityManager pour interagir avec la base de données
        $this->entityManager = $container->get(EntityManagerInterface::class);

        // Récupère le SluggerInterface pour la gestion des slugs
        $this->slugger = $container->get(SluggerInterface::class);

        // Commence une transaction pour pouvoir annuler les changements après le test
        $this->entityManager->beginTransaction();

        // Charge les fixtures nécessaires pour le test
        $this->loadFixtures([new CategoryFixtures($this->slugger)]);
    }

    /**
     * Méthode exécutée après chaque test pour nettoyer les ressources utilisées.
     */
    protected function tearDown(): void
    {
        // Vérifie si une transaction est active
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            // Annule la transaction pour annuler tous les changements faits durant le test
            $this->entityManager->rollback();
        }

        // Ferme l'EntityManager pour libérer les ressources
        $this->entityManager->close();
        $this->entityManager ;

        // Appelle la méthode tearDown de la classe parente
        parent::tearDown();
    }

    /**
     * Charge les fixtures spécifiées dans la base de données.
     *
     * @param array $fixtureInstances Instances de fixtures à charger.
     */
    protected function loadFixtures(array $fixtureInstances): void
    {
        // Crée un chargeur de fixtures
        $loader = new Loader();

        // Ajoute chaque fixture au chargeur
        foreach ($fixtureInstances as $fixture) {
            $loader->addFixture($fixture);
        }

        // Crée un purger pour nettoyer la base de données
        $purger = new ORMPurger();

        // Crée un exécuteur pour exécuter les fixtures
        $executor = new ORMExecutor($this->entityManager, $purger);

        // Exécute les fixtures en mode append (ne supprime pas les données existantes)
        $executor->execute($loader->getFixtures(), true);
    }

    /**
     * Teste le chargement des catégories à partir des fixtures.
     */
    public function testLoadCategories(): void
    {
        // Récupère toutes les catégories depuis la base de données
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        // Vérifie que 30 catégories ont été chargées
        self::assertCount(30, $categories);

        // Vérifie que chaque catégorie a un nom et un slug non vides
        foreach ($categories as $category) {
            self::assertNotEmpty($category->getName());
            self::assertNotEmpty($category->getSlug());
        }
    }
}
