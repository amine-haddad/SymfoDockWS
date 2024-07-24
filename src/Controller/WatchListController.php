<?php

// src/Controller/WatchListController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Entity\Program;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Psr\Log\LoggerInterface;

class WatchListController extends AbstractController
{
    #[Route('/watchlist', name: 'watchlist')]
    public function index(){
        $user = $this->getUser();
        
        
        return $this->render('watchlist/index.html.twig', [
            'user' => $user,
        ]);
    }
}