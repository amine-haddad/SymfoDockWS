<?php

namespace App\Tests\Integration\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeasonControllerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private SeasonRepository $seasonRepository;
    protected function setUp(): void
    {
        // Boot the Symfony kernel
        $kernel = self::bootKernel();

        // Get the entity manager
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSomething(): void
    {
        $this->assertTrue(true);
    }
    
}
