<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Actor;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ProgramTest extends TestCase
{

    public function testAddSeason(): void
    {
        $program = new Program();
        $season = $this->createMock(Season::class);
        $season->method('getProgram')
            ->willReturn($program); // Configure le mock pour retourner l'entité Program.

        $program->addSeason($season);

        $this->assertTrue($program->getSeasons()->contains($season));
        $this->assertSame($program, $season->getProgram());
    }

    public function testRemoveSeason(): void
    {
        $program = new Program();
        $season = $this->createMock(Season::class);
        $season->method('getProgram')
            ->willReturn($program);

        $program->addSeason($season);
        $program->removeSeason($season);

        $this->assertFalse($program->getSeasons()->contains($season));
    }

    public function testAddActor(): void
    {
        $program = new Program();
        $actor = $this->createMock(Actor::class);

        $program->addActor($actor);

        $this->assertTrue($program->getActors()->contains($actor));
    }

    public function testRemoveActor(): void
    {
        $program = new Program();
        $actor = $this->createMock(Actor::class);

        $program->addActor($actor);
        $program->removeActor($actor);

        $this->assertFalse($program->getActors()->contains($actor));
    }

    public function testAddViewer(): void
    {
        $program = new Program();
        $user = $this->createMock(User::class);

        // Configure the mock to return a watchlist that can accept items
        $watchlist = new ArrayCollection();
        $user->method('getWatchlist')
            ->willReturn($watchlist);

        // Set expectations for addToWatchlist
        $user->expects($this->once())
            ->method('addToWatchlist')
            ->with($this->equalTo($program));

        $program->addViewer($user);

        $this->assertTrue($program->getViewers()->contains($user));
    }

    public function testRemoveViewer(): void
    {
        $program = new Program();
        $user = $this->createMock(User::class);

        // Configure the mock to return a watchlist that can accept items
        $watchlist = new ArrayCollection();
        $user->method('getWatchlist')
            ->willReturn($watchlist);

        // Expect addToWatchlist to be called
        $user->expects($this->once())
            ->method('addToWatchlist')
            ->with($this->equalTo($program));

        $program->addViewer($user);

        // Set expectations for removeFromWatchlist
        $user->expects($this->once())
            ->method('removeFromWatchlist')
            ->with($this->equalTo($program));

        $program->removeViewer($user);

        $this->assertFalse($program->getViewers()->contains($user));
    }

    public function testRemoveNonExistingViewer(): void
    {
        $program = new Program();
        $user = $this->createMock(User::class);

        // Essayez de supprimer un utilisateur qui n'est pas dans la liste des viewers
        $program->removeViewer($user);

        // Vérifiez que la liste des viewers reste inchangée
        $this->assertFalse($program->getViewers()->contains($user));
    }

    public function testAddSeasonWhenSeasonAlreadyExists(): void
    {
        $program = new Program();
        $season = $this->createMock(Season::class);

        $program->addSeason($season);
        $program->addSeason($season); // Ajouter la même saison une seconde fois

        $this->assertTrue($program->getSeasons()->contains($season));
        $this->assertCount(1, $program->getSeasons()); // Assurez-vous que la saison n’est pas dupliquée
    }

}
