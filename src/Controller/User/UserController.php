<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

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

    /**
     * @Route("/profile", name="profile", methods={"GET"})
     *
     * @return Response
     */
    public function profile(): Response
    {
        $user = $this->getUser();

        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            ['groups' => 'user']
        );
    }
}
