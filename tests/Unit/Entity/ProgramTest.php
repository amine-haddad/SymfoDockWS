<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Actor;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class ProgramTest extends TestCase
{
    private ?EntityManagerInterface $em = null;

    protected function setUp(): void
    {
        // Configurez le mock de l'EntityManager si nécessaire.
        $this->em = $this->createMock(EntityManagerInterface::class);
       
    }


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

    public function testSetTitle(): void
{
    $program = new Program();
    $title = 'Test Title';
    $program->setTitle($title);

    $this->assertEquals($title, $program->getTitle());
}

public function testSetPoster(): void
{
    $program = new Program();
    $poster = 'poster-image.jpg';
    $program->setPoster($poster);

    $this->assertEquals($poster, $program->getPoster());
}

public function testSetUpdatedAt(): void
{
    $program = new Program();
    $updatedAt = new \DateTime();
    $program->setUpdatedAt($updatedAt);

    $this->assertEquals($updatedAt, $program->getUpdatedAt());
}

public function testSetOwner(): void
{
    $program = new Program();
    $user = new User(); // Assurez-vous que User est correctement défini
    $program->setOwner($user);

    $this->assertSame($user, $program->getOwner());
}

public function testSetPosterFile(): void
    {
        // Créez un fichier temporaire pour le test
        $tempFilePath = sys_get_temp_dir() . '/test-file.jpg';
        file_put_contents($tempFilePath, 'test content'); // Crée un fichier temporaire

        // Créez un objet File avec le fichier temporaire
        $file = new File($tempFilePath);

        // Créez une instance de l'entité Program
        $program = new Program();
        
        // Définir le fichier dans l'entité
        $program->setPosterFile($file);
        
        // Vérifiez que le fichier est bien associé à l'entité
        $this->assertSame($file, $program->getPosterFile());
        
        // Nettoyez le fichier temporaire après le test
        unlink($tempFilePath);
    }

    public function testGetAndSetId(): void
    {
        $program = new Program();
        $reflection = new \ReflectionClass($program);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($program, 1);
        $this->assertSame(1, $program->getId());
    }

    public function testGetAndSetTitle(): void
    {
        $program = new Program();
        $title = 'Test Title';
        $program->setTitle($title);
        $this->assertSame($title, $program->getTitle());
    }

    public function testGetAndSetSynopsis(): void
    {
        $program = new Program();
        $synopsis = 'Test Synopsis';
        $program->setSynopsis($synopsis);
        $this->assertSame($synopsis, $program->getSynopsis());
    }

    public function testGetAndSetSlug(): void
    {
        $program = new Program();
        $slug = 'test-slug';
        $program->setSlug($slug);
        $this->assertSame($slug, $program->getSlug());
    }


}
