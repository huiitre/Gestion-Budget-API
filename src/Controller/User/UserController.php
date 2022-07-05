<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Models\JsonError;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /**
     * @Route("/signup", name="signup")
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param SerializerInterface $ser
     * @param ValidatorInterface $val
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function create(Request $req, EntityManagerInterface $em, SerializerInterface $ser, ValidatorInterface $val, UserPasswordHasherInterface $hasher): Response
    {
        $data = $req->getContent();
        dd($data);
        try {
            $user = $ser->deserialize($data, User::class, 'json');
        } catch (Exception $e) {
            return new JsonResponse('JSON invalide : ' . $e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $errors = $val->validate($user);
        if (count($errors) > 0) {
            $jsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, 'Des erreurs de validation ont été trouvées.');
            $jsonError->setValidationErrors($errors);
            return $this->json($jsonError, $jsonError->getError());
        }

        $userHasher = $hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($userHasher);

        $em->persist($user);

        try {
            $em->flush();
            return $this->json(
                $user,
                response::HTTP_CREATED,
                [],
                ['groups' => 'get_users']
            );
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse("Erreur", Response::HTTP_CONFLICT);
        }
    }
}
