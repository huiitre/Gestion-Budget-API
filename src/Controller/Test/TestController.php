<?php

namespace App\Controller\Test;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;

class TestController extends AbstractController
{
    /**
     * @Route("/api/test", name="api_test")
     */
    public function index(CategoryRepository $categoryRepo): Response
    {
        /* return $this->json(
            $categoryRepo,
            200,
            []
        ); */
        $categories = $categoryRepo->findAll();
        return $this->render(
            'test/index.html.twig', [
                'categories' => $categories
            ]
        );
    }
}
