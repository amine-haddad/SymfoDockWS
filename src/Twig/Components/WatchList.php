<?php

namespace App\Twig\Components;

use App\Entity\Program;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[AsLiveComponent('WatchList')]
final class WatchList
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public Program $program;

    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    #[LiveAction]
    public function toggleWatchList(): void
    {
        $user = $this->security->getUser();

        if ($user->getWatchlist()->contains($this->program)) {
            $user->removeFromWatchlist($this->program);
        } else {
            $user->addToWatchlist($this->program);
        }

        $this->entityManager->flush();
    }
}
