<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Program;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
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

    /**
     * Displays a list of all programs.
     *
     * @Route("/", name="index")
     *
     * @param ProgramRepository $programRepository Repository for Program entity
     * @return Response Returns a Response object with rendered template
     */
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        // Fetch all programs from the database
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
     * @param Program $program The program entity to be displayed.
     * @return Response Returns a Response object with rendered template for program details.
     * @throws \Exception If the program entity is not found.
     */
    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'], name: 'show')]
    public function show(Program $program): Response
    {
        // Fetch all seasons related to the program
        $seasons = $program->getSeasons();

        // Render the template with the program and its seasons
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * Displays a specific season of a program with its episodes.
     *
     * @Route("/{programId}/season/{seasonId}", requirements={"programId"="\d+", "seasonId"="\d+"}, methods={"GET"}, name="season_show")
     *
     * @param int $programId The id of the program.
     * @param int $seasonId The id of the season.
     * @return Response Returns a Response object with rendered template for season details.
     * @throws NotFoundHttpException If the program or season is not found.
     */
    #[Route('/{programId}/season/{seasonId}', requirements: ['programId' => '\d+', 'seasonId' => '\d+'], methods: ['GET'], name: 'season_show')]
    public function showSeason(int $programId, int $seasonId): Response
    {
        // Fetch the program from the database
        $program = $this->programRepository->find($programId);

        // If the program is not found, throw a NotFoundHttpException
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id: ' . $programId . ' found.'
            );
        }

        // Fetch the season from the database
        $season = $this->seasonRepository->find($seasonId);

        // If the season is not found or does not belong to the program, throw a NotFoundHttpException
        if (!$season || $season->getProgram()->getId() !== $programId) {
            throw $this->createNotFoundException(
                'No season with id: ' . $seasonId . ' found for program with id: ' . $programId
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
     *
     * @Route("/{programId}/season/{seasonId}/episode/{episodeId}", methods={"GET", "POST"}, name="episode_show")
     *
     * @param int $programId The id of the program.
     * @param int $seasonId The id of the season.
     * @param int $episodeId The id of the episode.
     * @return Response Returns a Response object with rendered template for episode details.
     * @throws NotFoundHttpException If the program, season, or episode is not found.
     */
    #[Route('/{programId}/season/{seasonId}/episode/{episodeId}', methods: ['GET', 'POST'], name: 'episode_show')]
    public function showEpisode(int $programId, int $seasonId, int $episodeId): Response
    {
        // Fetch the program from the database
        $program = $this->programRepository->find($programId);

        // If the program is not found, throw a NotFoundHttpException
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id: ' . $programId . ' found.'
            );
        }

        // Fetch the season from the database
        $season = $this->seasonRepository->find($seasonId);

        // If the season is not found or does not belong to the program, throw a NotFoundHttpException
        if (!$season || $season->getProgram()->getId() !== $programId) {
            throw $this->createNotFoundException(
                'No season with id: ' . $seasonId . ' found for program with id: ' . $programId
            );
        }

        // Fetch the episode from the database
        $episode = $this->episodeRepository->find($episodeId);

        // If the episode is not found or does not belong to the season, throw a NotFoundHttpException
        if (!$episode || $episode->getSeason()->getId() !== $seasonId) {
            throw $this->createNotFoundException(
                'No episode with id: ' . $episodeId . ' found for season with id: ' . $seasonId
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
