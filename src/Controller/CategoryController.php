<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index', methods: 'GET')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $action = $categoryRepository->findBy(['name' => 'action']);
        //dd($action);
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
        ]);
    }

    #[Route('/{categoryName}', name: 'show', methods: ['GET'])]
    public function show(string $categoryName, CategoryRepository $categoryRepository, EntityManagerInterface $programs, ProgramRepository $programRepository): Response
    {
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category : ' . $categoryName . ' found in category\'s table.'
            );
        }
        //dd($categoryName);
        $programs = $programRepository->findBy(
            ['category' => $category],
            ['id' => 'DESC'],// affiche les 3 dernier serie ajouter
            3 //limite de resultat affiche juste 3 series
        );
        if (count($programs) === 0) {
            throw $this->createNotFoundException(
                'No programs in this category :' . $categoryName . ' found in category\'s table.'
            );
        }
        //dd($programs);
        return $this->render('category/show.html.twig', [
            'categoryName' => $categoryName,
            'programs' => $programs,
        ]);
    }
}
