<?php

namespace App\Controller\Todolist;

use App\Entity\Todo;
use App\Entity\Todolist;
use App\Repository\TodolistRepository;
use App\Repository\TodoRepository;
use App\Service\MessageResponse;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function PHPUnit\Framework\isNull;

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

    /**
     * @Route("/create/list", name="create_list", methods={"POST"})
     *
     * @param Request $req
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function createTodolist(
        Request $req,
        SerializerInterface $serializer,
        TodolistRepository $tlr
    ): Response
    {
        $data = $req->getContent();
        $user = $this->getUser();

        //* stockage des messages d'erreur et du status
        $arrayMsg['msg'] = [];
        $status = 1;

        //* si l'objet n'existe pas, on retourne direct une réponse
        try {
            $list = $serializer->deserialize($data, Todolist::class, 'json');
        } catch (Exception $e) {
            return new JsonResponse(['msg' => ['Objet invalide'], 'status' => 0], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //* vérification du nom
        if (is_null($list->getName()) || $list->getName() == "") {
            array_push($arrayMsg['msg'], 'Le nom de la liste est vide');
            $status = 0;
        }
        
        //* vérification de la catégorie
        if (is_null($list->getCategory()) || $list->getCategory()->getId() == "") {
            array_push($arrayMsg['msg'], 'La catégorie est vide');
            $status = 0;
        }

        //* si le status est à 0, on retourne une erreur avec les différents messages
        if ($status == 0)
            return new JsonResponse(['msg' => $arrayMsg['msg'], 'status' => $status], Response::HTTP_UNPROCESSABLE_ENTITY);

        //* on appelle la base
        if ($tlr->createTodolist($list, $user) == 1) {
            array_push($arrayMsg['msg'], 'Ajout effectué');
            return $this->json(
                ['msg' => $arrayMsg['msg'], 'status' => 1],
                200,
                []
            );
        } else {
            array_push($arrayMsg['msg'], 'L\'ajout a échoué');
            return new JsonResponse(['msg' => $arrayMsg['msg'], 'status' => 0], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Création d'un todo
     * @Route("/create/todo", name="create_todo", methods={"POST"})
     *
     * @param Request $req
     * @param SerializerInterface $serializer
     * @param TodoRepository $tr
     * @return void
     */
    public function createTodo(
        Request $req,
        SerializerInterface $serializer,
        TodoRepository $tr,
        MessageResponse $msg
    )
    {
        $data = $req->getContent();
        $user = $this->getUser();

        $status = 1;

        //* si l'objet n'existe pas, on retourne direct une réponse
        try {
            $todo = $serializer->deserialize($data, Todo::class, 'json');
        } catch (Exception $e) {
            return new JsonResponse(['msg' => ['Objet invalide'], 'status' => 0], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        if (empty($todo->getName())) {
            $msg->setMsg('Le nom du todo est vide');
            $status = 0;
        }

        if (is_null($todo->getTodolist()) || $todo->getTodolist()->getId() == "") {
            $msg->setMsg('La todolist est vide ou n\'existe pas');
            $status = 0;
        }

        if ($status == 0)
            return new JsonResponse(['msg' => $msg->getMsg(), 'status' => $status], Response::HTTP_UNPROCESSABLE_ENTITY);

        if ($tr->createTodo($user, $todo) > 0) {
            $msg->setMsg('Ajout effectué');
            return $this->json(
                ['msg' => $msg->getMsg(), 'status' => 1],
                200,
                []
            );
        } else {
            $msg->setMsg('L\'ajout a échoué');
            return new JsonResponse(['msg' => $msg->getMsg(), 'status' => 0], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route("/delete/list", name="delete_list", methods={"DELETE"})
     *
     * @param Request $req
     * @param TodolistRepository $tlr
     * @return void
     */
    public function deleteTodolist(Request $req, TodolistRepository $tlr, SerializerInterface $serializer, MessageResponse $msg)
    {
        $user = $this->getUser();
        $data = $req->getContent();
        $status = 1;

        //* on vérifie si c'est un objet
        if (empty($data)) {
            $msg->setMsg('Objet invalide');
            $status = 0;
        }
        
        //* on sérialise l'objet
        $data = json_decode($data);

        //* on vérifie si l'objet est rempli, si c'est un array, et que ce dernier ne soit pas vide
        if (empty($data) || !is_array($data->ids) || empty($data->ids)) {
            $msg->setMsg('Il faut sélectionner au moins une liste');
            $status = 0;
        }

        if ($status == 0)
            return new JsonResponse(['msg' => $msg->getMsg(), 'status' => $status], Response::HTTP_UNPROCESSABLE_ENTITY);

        if ($tlr->deleteTodolist($data->ids, $user) > 0) {
            $msg->setMsg('La suppression a bien été effectué');
            $status = 1;
            return $this->json(
                ['msg' => $msg->getMsg(), 'status' => $status],
                200,
                []
            );
        } else {
            $msg->setMsg('Erreur lors de la suppression');
            $status = 0;
            return new JsonResponse(['msg' => $msg->getMsg(), 'status' => $status], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Suppression d'un todo d'une liste
     * 
     * @Route("/delete/todo", name="delete_todo", methods={"DELETE"})
     *
     * @param Request $req
     * @param MessageResponse $msg
     * @param TodoRepository $tr
     * @return void
     */
    public function deleteTodo(Request $req, MessageResponse $msg, TodoRepository $tr)
    {
        $user = $this->getUser();
        $data = $req->getContent();
        $status = 1;
        
        //* on vérifie si c'est un objet
        if (empty($data)) {
            $msg->setMsg('Objet invalide');
            $status = 0;
        }
        
        //* on sérialise l'objet
        $data = json_decode($data);

        //* on vérifie si l'objet est rempli, si c'est un array, et que ce dernier ne soit pas vide
        if (empty($data) || !is_array($data->ids) || empty($data->ids)) {
            $msg->setMsg('Il faut sélectionner au moins un todo');
            $status = 0;
        }

        //* si vide ou non integer
        if (empty($data->list) || !is_integer($data->list)) {
            $msg->setMsg('La todolist n\'est pas renseigné');
            $status = 0;
        }

        //* si status à 0 on return les erreurs
        if ($status == 0)
            return new JsonResponse(['msg' => $msg->getMsg(), 'status' => $status], Response::HTTP_UNPROCESSABLE_ENTITY);

        if ($tr->deleteTodo($data->ids, $user, $data->list) > 0) {
            $msg->setMsg('La suppression a bien été effectué');
            $status = 1;
            return $this->json(
                ['msg' => $msg->getMsg(), 'status' => $status],
                200,
                []
            );
        } else {
            $msg->setMsg('Erreur lors de la suppression');
            $status = 0;
            return new JsonResponse(['msg' => $msg->getMsg(), 'status' => $status], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}