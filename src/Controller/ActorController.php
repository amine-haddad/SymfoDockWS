<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorType;
use App\Repository\ActorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;


#[Route('/actor', name: 'actor_')]
class ActorController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ActorRepository $actorRepository): Response
    {
        $actors = $actorRepository->findAll();
        return $this->render('actor/index.html.twig', [
            'actors' => $actors,
        ]);
    }

    #[Route('/new', methods: ['GET', 'POST'], name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $actor = NEW Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $entityManager->persist($actor);
            $entityManager->flush();
            $this->addFlash('success', 'The new actor has been created');
            return $this->redirectToRoute('actor_show',['actor_id' => $actor->getId()]);
        }
        return $this->render('actor/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{actor_id}', methods: ['GET'], name: 'show')]
    function show(
        #[MapEntity(mapping: ['actor_id' => 'id'])] Actor $actor
    ) : Response
    {
        $programs = $actor->getPrograms();

        // Render the template with the program and its seasons
        return $this->render('actor/show.html.twig', [
            'programs' => $programs,
            'actor' => $actor,
        ]);
    }
}
