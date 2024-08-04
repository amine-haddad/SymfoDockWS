<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $commentRepository;
    private $userRepository;
    private $episodeRepository;

    /**
     * Initialisation avant chaque test.
     * Démarre le noyau Symfony et récupère les services nécessaires.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->commentRepository = $this->entityManager->getRepository(Comment::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->episodeRepository = $this->entityManager->getRepository(Episode::class);
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
     * Crée un utilisateur avec des rôles spécifiés pour les tests.
     */
    private function createUser($roles = ['ROLE_CONTRIBUTOR']): User
    {
        $email = 'testComment' . uniqid() . '@example.com';
        $user = new User();
        $user->setEmail($email);
        $user->setPassword('password');
        $user->setRoles($roles);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Crée un épisode pour les tests.
     */
    private function createEpisode(): Episode
    {
        $episode = new Episode();
        $episode->setTitle('Test Episode');
        $episode->setSynopsis('This is a test episode');
        $episode->setNumber(1);
        $episode->setDuration(60);
        $episode->setSlug('test-episode');

        $this->entityManager->persist($episode);
        $this->entityManager->flush();

        return $episode;
    }

    /**
     * Teste la création d'un commentaire.
     */
    public function testCreateComment(): void
    {
        $user = $this->createUser();
        $episode = $this->createEpisode();

        $comment = new Comment();
        $comment->setComment('This is a test comment');
        $comment->setRate(5);
        $comment->setEpisode($episode);
        $comment->setAuthor($user);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        $savedComment = $this->commentRepository->find($comment->getId());

        // Vérifie que le commentaire a été correctement sauvegardé
        $this->assertNotNull($savedComment, 'Comment should be saved.');
        $this->assertSame($comment->getComment(), $savedComment->getComment(), 'Comment content should match.');
        $this->assertSame($user->getId(), $savedComment->getAuthor()->getId(), 'Comment author should match.');
    }

    /**
     * Teste la suppression d'un commentaire.
     */
    public function testDeleteComment(): void
    {
        // Crée un utilisateur et un épisode pour le commentaire
        $user = $this->createUser();
        $episode = $this->createEpisode();

        // Crée une nouvelle instance de Comment
        $comment = new Comment();
        $comment->setComment('Test Comment');
        $comment->setRate(5);
        $comment->setEpisode($episode);
        $comment->setAuthor($user);

        // Persiste le commentaire dans la base de données
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        // Récupère l'identifiant du commentaire
        $commentId = $comment->getId();
        $this->assertNotNull($commentId, 'Comment ID should not be null after persisting.');

        // Supprime le commentaire de la base de données
        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        // Vérifie que le commentaire a bien été supprimé
        $deletedComment = $this->commentRepository->find($commentId);
        $this->assertNull($deletedComment, 'Comment should be null after deletion.');
    }

    /**
     * Teste la récupération de tous les commentaires.
     */
    public function testFindAllComments(): void
    {
        // Compte le nombre de commentaires avant d'en ajouter de nouveaux
        $initialCount = $this->commentRepository->count([]);

        $user = $this->createUser();
        $episode = $this->createEpisode();

        $comment1 = new Comment();
        $comment1->setComment('First comment');
        $comment1->setRate(2);
        $comment1->setEpisode($episode);
        $comment1->setAuthor($user);

        $comment2 = new Comment();
        $comment2->setComment('Second comment');
        $comment2->setRate(4);
        $comment2->setEpisode($episode);
        $comment2->setAuthor($user);

        $this->entityManager->persist($comment1);
        $this->entityManager->persist($comment2);
        $this->entityManager->flush();

        // Récupère tous les commentaires depuis la base de données
        $comments = $this->commentRepository->findAll();

        // Vérifie que le nombre de commentaires dans la base de données est correct
        $this->assertCount($initialCount + 2, $comments, 'The number of comments should match the count after adding.');
    }
}
