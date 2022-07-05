<?php

namespace App\Controller\Category;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/category", name="api_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods={"GET"})
     */
    public function categoriesList(CategoryRepository $cr): Response
    {
        return $this->json(
            $cr->findAll(),
            200,
            [],
            ['groups' => [
                'get_categories',
                ]
            ]
        );
    }
}