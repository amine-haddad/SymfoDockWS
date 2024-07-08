<?php

// src/Controller/CategoryController.php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index', methods: 'GET')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new (Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        // on recupere les données via la requete http, avec le methode handleRequest on hydrate $request.
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            $entityManager->persist($category);
            $entityManager->flush();

            // Redirect to categories list
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{categoryName}', name: 'show', methods: ['GET'])]
    public function show(
        string $categoryName,
        CategoryRepository $categoryRepository,
        ProgramRepository $programRepository
    ): Response {
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category : ' . $categoryName . ' found in category\'s table.'
            );
        }

        // Find the index of the current category
        $currentIndex = array_search($category, $categories);

        // Determine the previous and next categories
        $previousCategory = $currentIndex > 0 ? $categories[$currentIndex - 1] : null;
        $nextCategory = $currentIndex < count($categories) - 1 ? $categories[$currentIndex + 1] : null;

        $programs = $programRepository->findBy(
            ['category' => $category],
            ['id' => 'DESC'], // sort by newest
            3// limit to 3 results
        );

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'programs' => $programs,
            'previousCategory' => $previousCategory,
            'nextCategory' => $nextCategory,
        ]);
    }

}
