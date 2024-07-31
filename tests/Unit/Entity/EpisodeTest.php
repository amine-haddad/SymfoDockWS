<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Episode;
use App\Entity\Comment;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class EpisodeTest extends TestCase
{
    private Episode $episode;

    protected function setUp(): void
    {
        $this->episode = new Episode();
    }

    public function testInitialProperties(): void
    {
        $this->assertNull($this->episode->getId());
        $this->assertNull($this->episode->getTitle());
        $this->assertNull($this->episode->getSynopsis());
        $this->assertNull($this->episode->getNumber());
        $this->assertNull($this->episode->getSeason());
        $this->assertNull($this->episode->getDuration());
        $this->assertNull($this->episode->getSlug());
        $this->assertInstanceOf(ArrayCollection::class, $this->episode->getComments());
        $this->assertCount(0, $this->episode->getComments());
    }

    public function testSettersAndGetters(): void
    {
        $this->episode->setTitle('Test Title');
        $this->episode->setSynopsis('Test Synopsis');
        $this->episode->setNumber(1);
        $this->episode->setDuration(42);
        $this->episode->setSlug('test-slug');

        $this->assertEquals('Test Title', $this->episode->getTitle());
        $this->assertEquals('Test Synopsis', $this->episode->getSynopsis());
        $this->assertEquals(1, $this->episode->getNumber());
        $this->assertEquals(42, $this->episode->getDuration());
        $this->assertEquals('test-slug', $this->episode->getSlug());
    }

    public function testAddComment(): void
    {
        $comment = new Comment();
        $comment->setComment('Test Comment');

        $this->episode->addComment($comment);

        $this->assertCount(1, $this->episode->getComments());
        $this->assertTrue($this->episode->getComments()->contains($comment));
        $this->assertSame($this->episode, $comment->getEpisode());
    }

    public function testRemoveComment(): void
    {
        $comment = new Comment();
        $comment->setComment('Test Comment');

        $this->episode->addComment($comment);
        $this->episode->removeComment($comment);

        $this->assertCount(0, $this->episode->getComments());
        $this->assertFalse($this->episode->getComments()->contains($comment));
        $this->assertNull($comment->getEpisode());
    }
}