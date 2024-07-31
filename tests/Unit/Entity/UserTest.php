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
        $user = new User();
        $comment = $this->createMock(Comment::class);

        $user->addComment($comment);
        $user->removeComment($comment);

        $this->assertFalse($user->getComments()->contains($comment));
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
        $user = new User();
        $program = $this->createMock(Program::class);

        $user->addProgram($program);
        $user->removeProgram($program);

        $this->assertFalse($user->getPrograms()->contains($program));
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
}
