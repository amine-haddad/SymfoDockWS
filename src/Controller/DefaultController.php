<?php declare(strict_types=1);
//src/Controller/DefaultController.php
namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
class DefaultController extends AbstractController

{
    
    private $programRepository;

    public function __construct(
        ProgramRepository $programRepository,
    
    ) {
        $this->programRepository = $programRepository;
    }
      
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        $program = $this->programRepository->findAll();
                return $this->render('index.html.twig', [
            'programs' => $program,
            'website' => 'Wild Series Docker',
         ]);
    }
}