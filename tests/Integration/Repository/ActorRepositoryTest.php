<?php

namespace App\Tests\Repository;

use App\Entity\Actor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActorRepositoryTest extends KernelTestCase
{
    private ValidatorInterface $validator;
    private $entityManager;
    private $actorRepository;

    /**
     * Démarre le noyau Symfony et initialise les services nécessaires pour les tests.
     */
    protected function setUp(): void
    {
        // Démarre le noyau Symfony
        self::bootKernel();
        $container = self::getContainer();

        // Récupère le validateur pour les validations d'entités
        $this->validator = $container->get(ValidatorInterface::class);
        // Récupère l'entité manager
        $this->entityManager = $container->get('doctrine')->getManager();
        // Récupère le repository pour l'entité Actor
        $this->actorRepository = $this->entityManager->getRepository(Actor::class);

        // Optionnel: Vider la base de données pour les tests si nécessaire
        // $this->clearDatabase();
    }

    /**
     * Nettoie après chaque test en fermant l'entité manager pour éviter les fuites de mémoire.
     */
    protected function tearDown(): void
    {
        // Ferme l'entité manager pour éviter les fuites de mémoire
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // Évite les fuites de mémoire en mettant l'entité manager à null
    }

    /**
     * Supprime toutes les entités Actor de la base de données.
     * Utilisé pour nettoyer la base de données avant ou après les tests.
     */
    private function clearDatabase(): void
    {
        $actors = $this->actorRepository->findAll(); // Récupère tous les acteurs
        foreach ($actors as $actor) {
            $this->entityManager->remove($actor); // Supprime chaque acteur
        }
        $this->entityManager->flush(); // Exécute la requête SQL pour supprimer les acteurs
    }

    /**
     * Teste la création et l'ajout d'un acteur dans la base de données.
     */
    public function testCreateActor(): void
    {
        // Crée une nouvelle instance de l'entité Actor
        $actor = new Actor();
        $actor->setName('Test Actor ' . uniqid()); // Définit un nom unique pour l'acteur
        $actor->setSlug('test-actor-' . uniqid()); // Définit un slug unique pour l'acteur
        $actor->setUpdatedAt(new \DateTime()); // Définit la date de mise à jour

        $this->entityManager->persist($actor); // Persiste l'acteur dans la base de données
        $this->entityManager->flush(); // Exécute la requête SQL pour insérer l'acteur

        // Récupère l'acteur à partir du repository
        $savedActor = $this->actorRepository->findOneBy(['name' => $actor->getName()]);

        // Vérifie que l'acteur a été correctement sauvegardé
        $this->assertNotNull($savedActor); // Vérifie que l'acteur existe
        $this->assertSame($actor->getName(), $savedActor->getName()); // Vérifie le nom de l'acteur

        // Nettoyage : supprime l'acteur ajouté
        $this->entityManager->remove($savedActor); // Supprime l'acteur de la base de données
        $this->entityManager->flush(); // Exécute la requête SQL pour supprimer l'acteur
    }

    /**
     * Teste la mise à jour d'un acteur existant dans la base de données.
     */
    public function testUpdateActor(): void
    {
        // Crée une nouvelle instance de l'entité Actor
        $actor = new Actor();
        $actor->setName('Old Name ' . uniqid()); // Définit un nom unique pour l'acteur
        $actor->setSlug('old-name-' . uniqid()); // Définit un slug unique pour l'acteur
        $actor->setUpdatedAt(new \DateTime()); // Définit la date de mise à jour

        $this->entityManager->persist($actor); // Persiste l'acteur dans la base de données
        $this->entityManager->flush(); // Exécute la requête SQL pour insérer l'acteur

        // Modifie les propriétés de l'acteur
        $actor->setName('New Name ' . uniqid()); // Définit un nouveau nom unique pour l'acteur
        $actor->setSlug('new-name-' . uniqid()); // Définit un nouveau slug unique pour l'acteur
        $this->entityManager->flush(); // Exécute la requête SQL pour mettre à jour l'acteur

        // Récupère l'acteur mis à jour à partir du repository
        $updatedActor = $this->actorRepository->findOneBy(['name' => $actor->getName()]);

        // Vérifie que l'acteur a été correctement mis à jour
        $this->assertNotNull($updatedActor); // Vérifie que l'acteur existe
        $this->assertSame($actor->getName(), $updatedActor->getName()); // Vérifie le nom de l'acteur mis à jour

        // Nettoyage : supprime l'acteur mis à jour
        $this->entityManager->remove($updatedActor); // Supprime l'acteur de la base de données
        $this->entityManager->flush(); // Exécute la requête SQL pour supprimer l'acteur
    }

    /**
     * Teste la suppression d'un acteur de la base de données.
     */
    public function testDeleteActor(): void
    {
        // Crée une nouvelle instance de l'entité Actor
        $actor = new Actor();
        $actor->setName('Test Actor ' . uniqid()); // Définit un nom unique pour l'acteur
        $actor->setSlug('test-actor-' . uniqid()); // Définit un slug unique pour l'acteur
        $actor->setUpdatedAt(new \DateTime()); // Définit la date de mise à jour

        $this->entityManager->persist($actor); // Persiste l'acteur dans la base de données
        $this->entityManager->flush(); // Exécute la requête SQL pour insérer l'acteur

        $actorId = $actor->getId(); // Récupère l'identifiant de l'acteur
        $this->entityManager->remove($actor); // Supprime l'acteur de la base de données
        $this->entityManager->flush(); // Exécute la requête SQL pour supprimer l'acteur

        // Vérifie que l'acteur a bien été supprimé
        $deletedActor = $this->actorRepository->find($actorId); // Essaye de retrouver l'acteur par son identifiant
        $this->assertNull($deletedActor); // Vérifie que l'acteur est null, donc supprimé
    }

    /**
     * Teste que l'on peut récupérer tous les acteurs depuis la base de données.
     */
    public function testFindAllActors(): void
    {
        // Compte le nombre d'acteurs avant d'en ajouter de nouveaux
        $initialCount = $this->actorRepository->count([]);

        // Crée deux nouveaux acteurs avec des noms uniques
        $actor1 = new Actor();
        $actor1->setName('Actor 1 ' . uniqid()); // Définit un nom unique pour le premier acteur
        $actor1->setSlug('actor-1-' . uniqid()); // Définit un slug unique pour le premier acteur
        $actor1->setUpdatedAt(new \DateTime()); // Définit la date de mise à jour pour le premier acteur

        $actor2 = new Actor();
        $actor2->setName('Actor 2 ' . uniqid()); // Définit un nom unique pour le deuxième acteur
        $actor2->setSlug('actor-2-' . uniqid()); // Définit un slug unique pour le deuxième acteur
        $actor2->setUpdatedAt(new \DateTime()); // Définit la date de mise à jour pour le deuxième acteur

        $this->entityManager->persist($actor1); // Persiste le premier acteur dans la base de données
        $this->entityManager->persist($actor2); // Persiste le deuxième acteur dans la base de données
        $this->entityManager->flush(); // Exécute la requête SQL pour insérer les acteurs

        // Récupère tous les acteurs depuis la base de données
        $actors = $this->actorRepository->findAll();

        // Vérifie que le nombre d'acteurs dans la base de données a augmenté du nombre attendu
        $this->assertCount($initialCount + 2, $actors); // Vérifie le nombre d'acteurs

        // Nettoyage : supprime les acteurs ajoutés
        $this->entityManager->remove($actor1); // Supprime le premier acteur de la base de données
        $this->entityManager->remove($actor2); // Supprime le deuxième acteur de la base de données
        $this->entityManager->flush(); // Exécute la requête SQL pour supprimer les acteurs
    }
}
