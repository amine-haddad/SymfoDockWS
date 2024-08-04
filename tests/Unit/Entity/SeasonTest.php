<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Episode;
use App\Entity\Season;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class SeasonTest extends TestCase
{
    public function testGetId(): void
    {
        $season = new Season();
        $reflection = new \ReflectionClass($season);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($season, 1);
        $this->assertSame(1, $season->getId());
    }
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
        $episode = $this->createMock(Episode::class);
        $season->addEpisode($episode);
        $this->assertTrue($season->getEpisodes()->contains($episode));
    }

    public function testRemoveEpisode()
    {
        // Créer une instance de Season
        $season = new Season();

        // Créer une instance d'Episode
        $episode = new Episode();
        
        // Associer l'Episode à la Season
        $season->addEpisode($episode);

        // Assurer que l'Episode est bien associé à la Season
        $this->assertSame($season, $episode->getSeason());

        // Appeler la méthode removeEpisode
        $season->removeEpisode($episode);

        // Vérifier que la Season de l'Episode est null
        $this->assertNull($episode->getSeason());
    }

}
