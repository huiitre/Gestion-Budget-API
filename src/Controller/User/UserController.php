<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user", name="api_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function userList(UserRepository $ur): Response
    {
        return $this->json(
            $ur->findAll(),
            200,
            [],
            ['groups' => [
                'get_users'
                ]
            ]
        );
    }
}
