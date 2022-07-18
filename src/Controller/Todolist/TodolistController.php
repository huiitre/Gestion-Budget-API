<?php

namespace App\Controller\Todolist;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route("/api/todolist", name="api_todolist_")
 */
class TodolistController extends AbstractController
{
    /**
     * Route("/list", name="list", methods={"GET"})
     *
     * @return Response
     */
    public function showList(): Response
    {
        $user = $this->getUser();
        return $this->json(
            'salut salut',
            200,
            []
        );
    }
}