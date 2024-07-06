<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProgramRepository;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
   #[Route('/', name: 'index')]
   public function index(ProgramRepository $programRepository): Response
   {
      $programs = $programRepository->findAll();
      return $this->render('program/index.html.twig', [
         'website' => 'Wild Series Docker',
         'programs' => $programs
      ]);
   }

   #[Route('/list/{page}', requirements: ['page' => '\d+'], methods: ['GET'], name: 'list')]
   public function list(int $page = 1): Response
   {
      return $this->render('program/list.html.twig', ['page' => $page]);
   }

   #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'], name: 'show')]
   public function show(int $id, ProgramRepository $programRepository): Response
   {
      $program = $programRepository->findOneBy(['id' => $id]);
      if (!$program) {
         throw $this->createNotFoundException(
            'No program with id : ' . $id . ' found in program\'s table.'
         );
      }
      return $this->render('program/show.html.twig', [
         'program' => $program,
      ]);
   }

   #[Route('/{programId}/season/{seasonId}', requirements: ['programId' => '\d+', 'seasonId' => '\d+'], methods: ['GET'], name:'season_show')]
   public function showSeason(Program $programs, Season $seasons){
      if (!$programs) {
         throw $this->createNotFoundException(
             'No program with id : ' . $programs . ' found in program\'s table.'
         );
     }
     if (!$seasons) {
         throw $this->createNotFoundException(
             'No season with id : ' . $seasons . ' found in season\'s table.'
         );
     }
     $episodes = $seasons->getEpisodes();
     if (!$episodes) {
         throw $this->createNotFoundException(
             'No episode with id : ' . $episodes . ' found in episode\'s table.'
         );
     }
      return $this->render('program/season_show.html.twig', [$programs => 'programs',
   $seasons => 'seasons']);
   }

   #[Route('/new', methods: ['GET', 'POST'], name: 'new')]
   public function new(): Response
   {
      return $this->redirectToRoute('program_show', ['id' => 4]);
   }
}