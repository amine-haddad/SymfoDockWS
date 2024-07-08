<?php
// src/Controller/ProgramController.php
namespace App\Controller;

//use App\Entity\Program;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    private $programRepository;
    private $seasonRepository;
    private $episodeRepository;

    /**
     * Constructor for ProgramController class.
     *
     * Initializes repositories for Program, Season, and Episode entities.
     *
     * @param ProgramRepository $programRepository Repository for Program entity
     * @param SeasonRepository $seasonRepository Repository for Season entity
     * @param EpisodeRepository $episodeRepository Repository for Episode entity
     */
    public function __construct(
        ProgramRepository $programRepository,
        SeasonRepository $seasonRepository,
        EpisodeRepository $episodeRepository
    ) {
        $this->programRepository = $programRepository;
        $this->seasonRepository = $seasonRepository;
        $this->episodeRepository = $episodeRepository;
    }

    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        // Render the template with the fetched programs
        return $this->render('program/index.html.twig', [
            'website' => 'Wild Series Docker',
            'programs' => $programs,
        ]);
    }

    /**
     * Displays a list of programs with pagination.
     *
     * @Route("/list/{page}", requirements={"page"="\d+"}, methods={"GET"}, name="list")
     *
     * @param int $page The current page number. Default is 1.
     * @return Response Returns a Response object with rendered template for program list.
     * @throws \Exception If the page number is not a positive integer.
     */
    #[Route('/list/{page}', requirements: ['page' => '\d+'], methods: ['GET'], name: 'list')]
    public function list(int $page = 1): Response
    {
        // Check if the page number is a positive integer
        if ($page <= 0) {
            throw new \Exception('Page number must be a positive integer.');
        }

        // Render the template with the current page number
        return $this->render('program/list.html.twig', ['page' => $page]);
    }

    /**
     * Displays a specific program with its seasons.
     *
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"}, name="show")
     *
     *
     * @return Response Returns a Response object with rendered template for program details.
     * @throws \Exception If the program entity is not found.
     */
    #[Route('/{program_id}', methods: ['GET'], name: 'show')]
    public function show(
        #[MapEntity(mapping: ['program_id' => 'id'])] Program $program
        ): Response
    
    {
        // Fetch all seasons related to the program
        $seasons = $program->getSeasons();

        // Render the template with the program and its seasons
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    #[Route("/{program_id}/comment/{comment_id}", name:"program_show_comment")]
public function showProgramComment(
    #[MapEntity(mapping: ['program_id' => 'id'])] Program $program, 
    //#[MapEntity(mapping: ['comment_id' => 'id'])] Comment $comment
): Response
{
  return $this->render('comment.html.twig', [
    'program' => $program,
    //'comment' => $comment,
  ]);
}

 
    #[Route('/{program_id}/season/{season_id}', methods: ['GET'], name: 'season_show')]
    public function showSeason(
        #[MapEntity(mapping: ['program_id' => 'id'])] Program $program,
        #[MapEntity(mapping: ['season_id' => 'id'])] Season  $season,
        ): Response
    {

        // If the program is not found, throw a NotFoundHttpException
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id: ' . 'program_id'. ' found.'
            );
        }


      

        // Fetch all episodes related to the season
        $episodes = $season->getEpisodes();

        // Render the template with the program, season, and episodes
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    /**
     * Displays a specific episode of a season of a program.
     * @return Response Returns a Response object with rendered template for episode details.
     * @throws NotFoundHttpException If the program, season, or episode is not found.
     */
    #[Route('/{program_id}/season/{season_id}/episode/{episode_id}', methods: ['GET', 'POST'], name: 'episode_show')]
    public function showEpisode(
        #[MapEntity(mapping: ['program_id' => 'id'])] Program $program,
        #[MapEntity(mapping: ['season_id' => 'id'])] Season  $season,
        #[MapEntity(mapping: ['episode_id' => 'id'])] Episode  $episode,
    
    ): Response
    {
        // Fetch the program from the database
        $program = $this->programRepository->find($program);

        // If the program is not found, throw a NotFoundHttpException
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id: ' . $program . ' found.'
            );
        }

        // Vérifiez si la saison appartient bien au programme
        if ($season->getProgram()->getId() !== $program->getId()) {
            throw new NotFoundHttpException(
                'No season with id: ' . $season->getId() . ' found for program with id: ' . $program->getId()
            );
        }

        // Vérifiez si l'épisode appartient bien à la saison
        if ($episode->getSeason()->getId() !== $season->getId()) {
            throw new NotFoundHttpException(
                'No episode with id: ' . $episode->getId() . ' found for season with id: ' . $season->getId()
            );
        }


        // Render the template with the program, season, and episode
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }/**
     * Redirects to the show action of ProgramController for a specific program.
     *
     * @Route("/new", methods={"GET", "POST"}, name="new")
     *
     * @return Response Returns a Response object that redirects to the show action of ProgramController for program with id 4.
     */
    #[Route('/new', methods: ['GET', 'POST'], name: 'new')]
    public function new (): Response
    {
        return $this->redirectToRoute('program_show', ['id' => 4]);
    }

}
