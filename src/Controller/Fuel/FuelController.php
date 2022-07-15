<?php

namespace App\Controller\Fuel;

use App\Entity\Fuel;
use App\Models\JsonError;
use App\Repository\CategoryRepository;
use App\Repository\FuelRepository;
use App\Repository\VehicleRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/fuel", name="api_fuel_")
 */
class FuelController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods={"GET"})
     */
    public function fuelList(FuelRepository $fr): Response
    {
        $result = $fr->findAll();
        
        return $this->json(
            $result,
            200,
            [],
            ['groups' => 'get_fuel']
        );
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     *
     * @param Request $req
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function createFuel(Request $req, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $data = $req->getContent();
        $user = $this->getUser();

        try {
            $newFuel = $serializer->deserialize($data, Fuel::class, 'json');
        } catch (Exception $e) {
            return new JsonResponse('JSON invalide : ' . $e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $errors = $validator->validate($newFuel);

        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, 'Des erreurs de validation ont été trouvées.');
            $myJsonError->setValidationErrors($errors);
            // dd($errors[0]);
            // return new JsonResponse($myJsonError, Response::HTTP_UNPROCESSABLE_ENTITY);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        $em->persist($newFuel);

        try {
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse('Erreur', Response::HTTP_CONFLICT);
        }

        return $this->json(
            $newFuel,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_fuel']
        );
    }
}