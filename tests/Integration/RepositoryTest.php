<?php

namespace App\Tests\Integration;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryRepositoryTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        // Boot the Symfony kernel
        $kernel = self::bootKernel();

        // Get the entity manager
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testAddCategory(): void
    {
        $category = new Category();
        $category->setName('Test Category');
        $category->setSlug('test-category');

        // Persist the new category
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        // Retrieve the category from the repository
        $savedCategory = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Test Category']);

        // Assert that the category was saved correctly
        $this->assertInstanceOf(Category::class, $savedCategory);
        $this->assertSame('Test Category', $savedCategory->getName());
        $this->assertSame('test-category', $savedCategory->getSlug());
    }

    protected function tearDown(): void
    {
        // Close the entity manager
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // Avoid memory leaks
    }
}
