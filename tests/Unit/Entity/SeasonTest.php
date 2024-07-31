<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Season;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class SeasonTest extends TestCase
{
    public function testConstructorInitializesEpisodesCollection(): void
    {
        $season = new Season();
        $this->assertInstanceOf(ArrayCollection::class, $season->getEpisodes());
        $this->assertEmpty($season->getEpisodes());
    }
    public function testSetNumber(): void
    {
        $season = new Season();
        $season->setNumber(1);
        $this->assertSame(1, $season->getNumber());
    }

    public function testSetYear(): void
    {
        $season = new Season();
        $season->setYear(2022);
        $this->assertSame(2022, $season->getYear());
    }

    public function testGetDescription(): void
    {
        $season = new Season();
        $season->setDescription('This is a test season.');
        $this->assertSame('This is a test season.', $season->getDescription());
    }

    public function testSetProgram(): void
    {
        $program = $this->createMock(\App\Entity\Program::class);
        $season = new Season();
        $season->setProgram($program);
        $this->assertSame($program, $season->getProgram());
    }

    public function testAddEpisodes(): void
    {
        $season = new Season();
        $episode = $this->createMock(\App\Entity\Episode::class);
        $season->addEpisode($episode);
        $this->assertTrue($season->getEpisodes()->contains($episode));
    }

    public function testRemoveEpisodes(): void
    {
        $season = new Season();
        $episode = $this->createMock(\App\Entity\Episode::class);
        $season->addEpisode($episode);
        $season->removeEpisode($episode);
        $this->assertFalse($season->getEpisodes()->contains($episode));
    }

}
