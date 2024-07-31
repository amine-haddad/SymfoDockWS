<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Actor;
use App\Entity\Program;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Tests\Fake\FakeFile;

class ActorTest extends TestCase
{
    private Actor $actor;

    protected function setUp(): void
    {
        $this->actor = new Actor();
    }

    public function testInitialProperties(): void
    {
        $this->assertNull($this->actor->getId());
        $this->assertNull($this->actor->getName());
        $this->assertNull($this->actor->getPhoto());
        $this->assertNull($this->actor->getPhotoFile());
        $this->assertNull($this->actor->getSlug());
        $this->assertEmpty($this->actor->getPrograms());
    }

    public function testSettersAndGetters(): void
    {
        $this->actor->setName('Test Actor');
        $this->actor->setPhoto('test-photo.jpg');
        $this->actor->setSlug('test-slug');

        // Testing getter methods
        $this->assertEquals('Test Actor', $this->actor->getName());
        $this->assertEquals('test-photo.jpg', $this->actor->getPhoto());
        $this->assertEquals('test-slug', $this->actor->getSlug());

        // PhotoFile should be null by default
        $this->assertNull($this->actor->getPhotoFile());

        // Testing setting a null file
        $this->actor->setPhotoFile(null);
        $this->assertNull($this->actor->getPhotoFile());
    }

    public function testAddAndRemoveProgram(): void
    {
        $program = new Program();
        $this->actor->addProgram($program);

        $this->assertCount(1, $this->actor->getPrograms());
        $this->assertTrue($this->actor->getPrograms()->contains($program));

        $this->actor->removeProgram($program);

        $this->assertCount(0, $this->actor->getPrograms());
        $this->assertFalse($this->actor->getPrograms()->contains($program));
    }
}
