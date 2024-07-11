<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/episode')]
class EpisodeController extends AbstractController
{

    private $emailService;

    public function __construct(

        EmailService $emailService
    ) {

        $this->emailService = $emailService;
    }
    #[Route('/', name: 'app_episode_index', methods: ['GET'])]
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_episode_new', methods: ['GET', 'POST'])]
    public function new (Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $episode = new Episode();

        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($episode->getTitle());
            $episode->setSlug($slug);
            $entityManager->persist($episode);
            $entityManager->flush();
            /*$email = (new Email())
            ->from($this->getParameter('mailer_from'))
            ->to('your_email@example.com')
            ->subject('Un nouvelle épisode vient d\'être publiée '.$episode->getTitle().' !')
            ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $episode]));

            $mailer->send($email);*/
            $this->emailService->sendNewEpisodeEmail($episode);
            $this->addFlash('success', 'The new program has been created');

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{slugy}', name: 'app_episode_show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['slugy' => 'slug'])] Episode $episode
    ): Response {
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    #[Route('/{slugy}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['slugy' => 'slug'])] Episode $episode,
        EntityManagerInterface $entityManager
    ): Response {

        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The program has been edited');

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{slugy}', name: 'app_episode_delete', methods: ['POST'])]
    public function delete(Request $request,
        #[MapEntity(mapping: ['slugy' => 'slug'])] Episode $episode,
        EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $episode->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($episode);
            $entityManager->flush();
            $this->addFlash('danger', 'The program has been deleted');
        }

        return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
    }
}
