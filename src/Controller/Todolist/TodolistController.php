<?php

namespace App\Controller\Todolist;

use App\Repository\TodolistRepository;
use App\Repository\TodoRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/todolist", name="api_todolist_")
 */
class TodolistController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods={"GET"})
     *
     * @return Response
     */
    public function showTodolist(TodolistRepository $tlr): Response
    {
        $user = $this->getUser();

        $data = $tlr->showTodolist($user);

        return $this->json(
            $data,
            200,
            []
        );
    }

    /**
     * @Route("/{id}/todos", name="todos", methods={"GET"})
     *
     * @param TodoRepository $tr
     * @param Request $req
     * @return Response
     */
    public function showTodosByList(TodoRepository $tr, $id): Response
    {
        $user = $this->getUser();

        $data = $tr->showTodos($user, $id);

        return $this->json(
            $data,
            200,
            []
        );
    }
}