<?php

namespace App\Tests\Unit\Entity;


use App\Entity\User;
use App\Entity\Program;
use App\Entity\Comment;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class UserTest extends TestCase
{
    public function testSetEmail(): void
    {
        $user = new User();
        $user->setEmail('john@example.com');

        $this->assertSame('john@example.com', $user->getEmail());
    }

    public function testSetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $user->getRoles());
    }

    public function testSetPassword(): void
    {
        $user = new User();
        $user->setPassword('hashed_password');

        $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testAddComment(): void
    {
        $user = new User();
        $comment = $this->createMock(Comment::class);

        $user->addComment($comment);

        $this->assertTrue($user->getComments()->contains($comment));
    }

    public function testRemoveComment(): void
{
    // Créez un utilisateur et un commentaire réels
    $user = new User();
    $comment = new Comment();

    // Ajoutez le commentaire à l'utilisateur
    $user->addComment($comment);

    // Assurez-vous que le commentaire est associé à l'utilisateur
    $this->assertTrue($comment->getAuthor() === $user);

    // Retirez le commentaire de l'utilisateur
    $user->removeComment($comment);

    // Vérifiez que le commentaire a été retiré de la collection
    $this->assertFalse($user->getComments()->contains($comment));

    // Assurez-vous que le commentaire n'a plus d'auteur
    $this->assertNull($comment->getAuthor());
}

    public function testAddProgram(): void
    {
        $user = new User();
        $program = $this->createMock(Program::class);

        $user->addProgram($program);

        $this->assertTrue($user->getPrograms()->contains($program));
    }

    public function testRemoveProgram(): void
{
    // Créez un utilisateur et un programme réels
    $user = new User();
    $program = new Program();

    // Ajoutez le programme à l'utilisateur
    $user->addProgram($program);

    // Assurez-vous que le programme est associé à l'utilisateur
    $this->assertTrue($program->getOwner() === $user);

    // Retirez le programme de l'utilisateur
    $user->removeProgram($program);

    // Vérifiez que le programme a été retiré de la collection
    $this->assertFalse($user->getPrograms()->contains($program));

    // Assurez-vous que le programme n'a plus d'utilisateur comme propriétaire
    $this->assertNull($program->getOwner());
}


    public function testAddToWatchlist(): void
    {
        $user = new User();
        $program = $this->createMock(Program::class);

        $user->addToWatchlist($program);

        $this->assertTrue($user->getWatchlist()->contains($program));
    }

    public function testRemoveFromWatchlist(): void
    {
        $user = new User();
        $program = $this->createMock(Program::class);

        $user->addToWatchlist($program);
        $user->removeFromWatchlist($program);

        $this->assertFalse($user->getWatchlist()->contains($program));
    }

    public function testIsVerified(): void
    {
        $user = new User();
        $user->setVerified(true);

        $this->assertTrue($user->isVerified());
    }

    public function testGetId(): void
    {
        $user = new User();
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);
        $this->assertSame(1, $user->getId());
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('john@example.com');
        $this->assertSame('john@example.com', $user->getUserIdentifier());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        // Assuming there's some temporary, sensitive data that needs to be cleared
        $user->eraseCredentials();
        $this->assertTrue(true); // This is a placeholder assertion as eraseCredentials doesn't currently do anything
    }

    

}
