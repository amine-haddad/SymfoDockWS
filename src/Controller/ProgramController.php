<?php
// src/Controller/ProgramController.php
namespace App\Controller;

//use App\Entity\Program;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentFormType;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Service\EmailService;
use App\Service\ProgramDuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    private $programRepository;
    private $seasonRepository;
    private $episodeRepository;
    private $emailService;

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
        EpisodeRepository $episodeRepository,
        EmailService $emailService
    ) {
        $this->programRepository = $programRepository;
        $this->seasonRepository = $seasonRepository;
        $this->episodeRepository = $episodeRepository;
        $this->emailService = $emailService;
    }
    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(SessionInterface $session, Request $request, ): Response
    {

        if (!$session->has('total')) {
            $session->set('total', 40); // if total doesn’t exist in session, it is initialized.
        }
        $total = $session->get('total'); // get actual value in session with ‘total' key.
        // ...
        // Create the associated Form
        $form = $this->createForm(SearchProgramType::class);
        // Get data from HTTP request
        $form->handleRequest($request);

        // Initialisation des programmes
        $programs = [];
        // Was the form is submitted?
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $this->programRepository->findLikeName($search);
            //dd($programs);
            //return $this->redirectToRoute('program_index', ['programs' => $programs]);
        } else {
            $programs = $this->programRepository->findAll();
        }
        //dd($programs);
        return $this->render('program/index.html.twig', [
            'website' => 'Wild Series Docker',
            'programs' => $programs,
            'form' => $form->createView(),
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
        if ($page <= 0) {
            throw new \Exception('Page number must be a positive integer.');
        }

        $programs = $this->programRepository->findAll();

        return $this->render('program/list.html.twig', [
            'programs' => $programs,
            'page' => $page,
        ]);
    }

    /**
     * Redirects to the show action of ProgramController for a specific program.
     *
     * @Route("/new", methods={"GET", "POST"}, name="new")
     *
     * @return Response Returns a Response object that redirects to the show action of ProgramController for program with id 4.
     */
    #[Route('/new', methods: ['GET', 'POST'], name: 'new')]
    public function new (Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        // Was the form submitted?
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            $entityManager->persist($program);
            $entityManager->flush();
            /*$email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to('your_email@example.com')
            ->subject('Une nouvelle série vient d\'être publiée'.$program->getTitle().' !')
            ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);*/
            // Appel du service EmailService pour envoyer l'email
            $this->emailService->sendNewProgramEmail($program);
            $this->addFlash('success', 'The new program has been created');
            return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
        }
        return $this->render('program/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/edit', methods: ['GET', 'POST'], name: 'edit')]
    public function edit(Program $program, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($this->getUser() !== $program->getOwner()) {
            // If not the owner, throws a 403 Access Denied exception
            throw $this->createAccessDeniedException('Only the owner can edit the program!');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $program->setSlug($slugger->slug($program->getTitle())); // Mettre à jour le slug si nécessaire

            $entityManager->flush();

            $this->addFlash('success', 'Program updated successfully.');

            return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()]);
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
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
    #[Route('/{slug}', methods: ['GET'], name: 'show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Program $program,
        ProgramDuration $programDuration
    ): Response {
        // Fetch all seasons related to the program
        $seasons = $program->getSeasons();
        $timeProgram = $programDuration->calculate($program);
        // Render the template with the program and its seasons
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
            'programDuration' => $timeProgram,
        ]);
    }

    #[Route("/{slug}/season/{season_id}/episode/{slugy}/comment/{comment_id}", name: "show_comment", methods: ['GET'])]
    public function showProgramComment(
        #[MapEntity(mapping: ['slug' => 'slug'])] Program $program,
        //#[MapEntity(mapping: ['comment_id' => 'id'])] Comment $comment
    ): Response {
        return $this->render('comment/show.html.twig', [
            'program' => $program,
            //'comment' => $comment,
        ]);
    }

    #[Route('/{slug}/season/{season_id}', methods: ['GET'], name: 'season_show')]
    public function showSeason(
        #[MapEntity(mapping: ['slug' => 'slug'])] Program $program,
        #[MapEntity(mapping: ['season_id' => 'id'])] Season $season,
    ): Response {

        // If the program is not found, throw a NotFoundHttpException
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id: ' . 'slug' . ' found.'
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
    #[Route('/{slug}/season/{season_id}/episode/{slugy}', methods: ['GET', 'POST'], name: 'episode_show')]
    public function showEpisode(Request $request, EntityManagerInterface $entityManager,
        #[MapEntity(mapping: ['slug' => 'slug'])] Program $program,
        #[MapEntity(mapping: ['season_id' => 'id'])] Season $season,
        #[MapEntity(mapping: ['slugy' => 'slug'])] Episode $episode,

    ): Response {
        // Fetch the program from the database
        $program = $this->programRepository->find($program);
        $comment = new Comment();
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

        $comments = $episode->getComments();

        $form = $this->createForm(CommentFormType::class, $comment);
        // Get data from HTTP request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);

            //For exemple : persiste & flush the entity
            // Persist Category Object
            $entityManager->persist($comment);
            //Flush the persisted object
            $entityManager->flush();
            return $this->redirectToRoute('program_episode_show', ['slug' => $program->getSlug(), 'season_id' => $season->getId(), 'slugy' => $episode->getSlug()]);
        }

        // Render the template with the program, season, and episode
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'comment' => $comment,
            'comments' => $comments,
            "form" => $form->createView(),
        ]);
    }

    #[Route('/{slug}/delete', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        #[MapEntity(mapping: ['slug' => 'slug'])] Program $program,
        #[MapEntity(mapping: ['season_id' => 'id'])] Season $season,

    ): Response {
        // Vérifiez si le token CSRF est valide
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();

            $this->addFlash('success', 'Program deleted successfully.');
        }

        return $this->redirectToRoute('program_index');
    }

    #[Route('/{slug}/season/{season_id}/episode/{slugy}/comment/{comment_id}/delete', name: 'delete_comment', methods: ['POST'])]
    public function deleteComment(Request $request, #[MapEntity(mapping: ['comment_id' => 'id'])] Comment $comment, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment deleted successfully.');
        }

        return $this->redirectToRoute('program_episode_show', [
            'slug' => $comment->getEpisode()->getSeason()->getProgram()->getSlug(),
            'season_id' => $comment->getEpisode()->getSeason()->getId(),
            'slugy' => $comment->getEpisode()->getSlug(),
        ]);
    }
}
