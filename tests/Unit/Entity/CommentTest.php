<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{

    private Comment $comment;

    protected function setUp(): void
    {
        $this->comment = new Comment();
    }

    public function testInitialProperties(): void
    {
        $this->assertNull($this->comment->getId());
        $this->assertNull($this->comment->getComment());
        $this->assertNull($this->comment->getRate());
        $this->assertNull($this->comment->getEpisode());
        $this->assertNull($this->comment->getAuthor());
    }

    public function testSettersAndGetters(): void
    {
        $commentText = 'This is a test comment.';
        $rate = 5;

        $this->comment->setComment($commentText);
        $this->comment->setRate($rate);

        $this->assertEquals($commentText, $this->comment->getComment());
        $this->assertEquals($rate, $this->comment->getRate());
    }

    public function testSetAndGetEpisode(): void
    {
        $episode = new Episode();
        $this->comment->setEpisode($episode);

        $this->assertSame($episode, $this->comment->getEpisode());
    }

    public function testSetAndGetAuthor(): void
    {
        $user = new User();
        $this->comment->setAuthor($user);

        $this->assertSame($user, $this->comment->getAuthor());
    }
}
