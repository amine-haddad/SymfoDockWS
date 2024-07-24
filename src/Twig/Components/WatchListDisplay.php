<?php

// src/Twig/Components/WatchListDisplay.php

namespace App\Twig\Components;

use App\Entity\Program;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('WatchListDisplay')]
final class WatchListDisplay
{
    use DefaultActionTrait;

    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    #[LiveProp]
    public array $programIds = []; // Change to hold only IDs

    public function mount(): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $this->programIds = array_map(fn($program) => $program->getId(), $user->getWatchlist()->toArray());
        }
    }

    public function getPrograms(): array
    {
        return $this->entityManager->getRepository(Program::class)
            ->findBy(['id' => $this->programIds]);
    }
}
