<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Category;
use App\Entity\Program;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        $this->category = new Category();
    }

    public function testInitialProperties(): void
    {
        $this->assertNull($this->category->getId());
        $this->assertNull($this->category->getName());
        $this->assertNull($this->category->getSlug());
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getPrograms());
        $this->assertEmpty($this->category->getPrograms());
    }

    public function testSettersAndGetters(): void
    {
        $name = 'Category Name';
        $slug = 'category-name';

        $this->category->setName($name);
        $this->category->setSlug($slug);

        $this->assertEquals($name, $this->category->getName());
        $this->assertEquals($slug, $this->category->getSlug());
    }

    public function testAddAndRemoveProgram(): void
    {
        $program = new Program();

        $this->category->addProgram($program);
        $this->assertTrue($this->category->getPrograms()->contains($program));

        $this->category->removeProgram($program);
        $this->assertFalse($this->category->getPrograms()->contains($program));
    }

    public function testToString(): void
    {
        $name = 'Category Name';
        $this->category->setName($name);

        $this->assertEquals($name, (string) $this->category);
    }
}
