<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryRepositoryTest extends KernelTestCase
{
    private $entityManager;

    /**
     * Démarre le noyau Symfony et initialise l'entité manager.
     */
    protected function setUp(): void
    {
        // Démarre le noyau Symfony
        $kernel = self::bootKernel();
        
        // Récupère l'entité manager à partir du conteneur
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Teste que l'on peut ajouter une catégorie et la récupérer correctement depuis le repository.
     */
    public function testAddCategory(): void
    {
        // Crée une nouvelle entité Category
        $category = new Category();
        $category->setName('Test Category'); // Définit le nom de la catégorie
        $category->setSlug('test-category'); // Définit le slug de la catégorie

        // Persiste la nouvelle catégorie dans la base de données
        $this->entityManager->persist($category);
        $this->entityManager->flush(); // Exécute la requête SQL pour insérer la catégorie

        // Récupère la catégorie depuis le repository par son nom
        $savedCategory = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Test Category']);

        // Vérifie que la catégorie a été correctement sauvegardée et récupérée
        $this->assertInstanceOf(Category::class, $savedCategory); // Vérifie que l'objet récupéré est bien une instance de Category
        $this->assertSame('Test Category', $savedCategory->getName()); // Vérifie le nom de la catégorie récupérée
        $this->assertSame('test-category', $savedCategory->getSlug()); // Vérifie le slug de la catégorie récupérée

        // Nettoyage : supprime la catégorie ajoutée de la base de données
        $this->entityManager->remove($savedCategory);
        $this->entityManager->flush(); // Exécute la requête SQL pour supprimer la catégorie
    }

    /**
     * Nettoie après chaque test en fermant l'entité manager.
     */
    protected function tearDown(): void
    {
        // Ferme l'entité manager pour éviter les fuites de mémoire
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // Évite les fuites de mémoire en mettant l'entité manager à null
    }
}
