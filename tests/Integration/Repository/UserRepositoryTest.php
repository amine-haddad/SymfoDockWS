<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Comment;
use App\Entity\Program;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{ 
    private ?EntityManagerInterface $entityManager = null;
    private ?UserRepository $userRepository = null;
    
    private ?UserPasswordHasherInterface $passwordHasher = null;
    
    // Propriété pour suivre les utilisateurs créés pendant les tests
    private array $createdUsers = [];
    
    /**
     * Boot the Symfony kernel and initialize the entity manager.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    /**
     * Test that retrieves a user by email and checks their real roles.
     *
     * This test ensures that the roles returned by the user entity are the actual roles assigned to the user.
     * It does not account for role hierarchy.
     */
    public function testFindByEmailRoleReel(): void
    {
        // Retrieve the user from the repository based on email
        $userFromRepository = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'contributor@monsite.com']);

        // Assert that the user exists
        $this->assertNotNull($userFromRepository);
        
        // Assert that the email of the user matches
        $this->assertSame('contributor@monsite.com', $userFromRepository->getEmail());
        
        // Retrieve the roles assigned to the user
        $roles = $userFromRepository->getRoles();
        
        // Assert that the user has the 'ROLE_CONTRIBUTOR' role
        $this->assertContains('ROLE_CONTRIBUTOR', $roles);
        
        // Assert that the user does not have the 'ROLE_ADMIN' role
        $this->assertNotContains('ROLE_ADMIN', $roles);
    }

    /**
     * Test that retrieves a user by email and checks their roles including hierarchy.
     *
     * This test ensures that the roles returned by the user entity include roles from the hierarchy.
     */
    public function testFindByEmailHierarchieRole(): void
    {
        // Retrieve the user from the repository based on email
        $userFromRepository = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'contributor@monsite.com']);

        // Assert that the user exists
        $this->assertNotNull($userFromRepository);
        
        // Assert that the email of the user matches
        $this->assertSame('contributor@monsite.com', $userFromRepository->getEmail());
        
        // Retrieve the roles assigned to the user, including those inherited
        $roles = $userFromRepository->getRoles();
        
        // Assert that the user has the 'ROLE_CONTRIBUTOR' role
        $this->assertContains('ROLE_CONTRIBUTOR', $roles);
        
        // Assert that the user also has the 'ROLE_USER' role due to role hierarchy
        $this->assertContains('ROLE_USER', $roles);
    }

    /**
     * Test that retrieves all users from the repository.
     *
     * This test ensures that the repository returns a non-empty list of users.
     */
    public function testFindAllUsers(): void
    {
        // Retrieve all users from the repository
        $users = $this->entityManager->getRepository(User::class)->findAll();
        
        // Assert that the list of users is not empty
        $this->assertNotEmpty($users);
    }

    public function testSettersAndGetters(): void
    {
        // Créer un nouvel utilisateur
        $user = new User();

        // Définir des valeurs
        $user->setEmail('test@example.com');
        $user->setPassword('hashed_password');
        $user->setRoles(['ROLE_USER']);
        $user->setVerified(true);

        // Vérifier les valeurs avec les getters
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('hashed_password', $user->getPassword());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertTrue($user->isVerified());
    }

    public function testAddRemoveComment(): void
    {
        $user = new User();
        $comment = new Comment(); // Assurez-vous que vous avez une classe Comment définie

        $user->addComment($comment);
        $this->assertTrue($user->getComments()->contains($comment));

        $user->removeComment($comment);
        $this->assertFalse($user->getComments()->contains($comment));
    }

    public function testAddRemoveProgram(): void
    {
        $user = new User();
        $program = new Program(); // Assurez-vous que vous avez une classe Program définie

        $user->addProgram($program);
        $this->assertTrue($user->getPrograms()->contains($program));

        $user->removeProgram($program);
        $this->assertFalse($user->getPrograms()->contains($program));
    }

    public function testAddRemoveFromWatchlist(): void
    {
        $user = new User();
        $program = new Program(); // Assurez-vous que vous avez une classe Program définie

        $user->addToWatchlist($program);
        $this->assertTrue($user->getWatchlist()->contains($program));

        $user->removeFromWatchlist($program);
        $this->assertFalse($user->getWatchlist()->contains($program));
    }
    /**
     * Clean up after each test by closing the entity manager.
     */
    protected function tearDown(): void
    {
        // Supprimer les utilisateurs créés pendant les tests
        foreach ($this->createdUsers as $user) {
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();

        // Fermer l'entity manager pour éviter les fuites de mémoire
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    public function testUpgradePasswordForValidUser(): void
{
    $email = 'test' . uniqid() . '@example.com'; // Générer un email unique
    $user = new User();
    $user->setEmail($email);
    $hashedOldPassword = $this->passwordHasher->hashPassword($user, 'old_password');
    $user->setPassword($hashedOldPassword);

    $this->entityManager->persist($user);
    $this->entityManager->flush();

     // Ajouter l'utilisateur à la liste des utilisateurs créés
     $this->createdUsers[] = $user;

    $newPassword = 'new_password';
    $hashedNewPassword = $this->passwordHasher->hashPassword($user, $newPassword);

    $this->userRepository->upgradePassword($user, $hashedNewPassword);

    $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());
    $this->assertTrue($this->passwordHasher->isPasswordValid($updatedUser, $newPassword));
}

public function testUpgradePasswordThrowsExceptionForInvalidUser(): void
{
    $this->expectException(UnsupportedUserException::class);
    $this->expectExceptionMessage('Instances of "Mock_PasswordAuthenticatedUserInterface');

    // Utilisation d'un mock pour un utilisateur invalide
    $invalidUser = $this->createMock(PasswordAuthenticatedUserInterface::class);

    // Passer le mock comme utilisateur invalide
    $this->userRepository->upgradePassword($invalidUser, 'new_hashed_password');
}


public function testUpgradePassword(): void
{
    // Créer un utilisateur valide
    $email = 'test' . uniqid() . '@example.com';
    $user = new User();
    $user->setEmail($email);
    $hashedOldPassword = $this->passwordHasher->hashPassword($user, 'old_password');
    $user->setPassword($hashedOldPassword);

    $this->entityManager->persist($user);
    $this->entityManager->flush();

    // Ajouter l'utilisateur à la liste des utilisateurs créés
    $this->createdUsers[] = $user;

    // Nouveau mot de passe et hashage
    $newPassword = 'new_password';
    $hashedNewPassword = $this->passwordHasher->hashPassword($user, $newPassword);

    // Appeler la méthode upgradePassword
    $this->userRepository->upgradePassword($user, $hashedNewPassword);

    // Récupérer l'utilisateur mis à jour
    $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());

    // Vérifier que le mot de passe a été mis à jour
    $this->assertTrue($this->passwordHasher->isPasswordValid($updatedUser, $newPassword));
}



}
