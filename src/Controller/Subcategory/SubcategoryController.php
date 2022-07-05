<?php

namespace App\Controller\Subcategory;

use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/subcategory", name="api_subcategory_")
 */
class SubcategoryController extends AbstractController
{
    /**
     * @Route("/list/{id}", name="list", methods={"GET"})
     *
     * @param SubcategoryRepository $scr
     * @param [type] $id
     * @return Response
     */
    public function subcategoriesByCategory(SubcategoryRepository $scr, CategoryRepository $cr, $id): Response
    {
        $category = $cr->findBy(['id' => $id]);

        return $this->json(
            $scr->findBy(['category' => $category]),
            200,
            [],
            ['groups' => [
                'get_subcategories',
                ]
            ]
        );
    }
}
