<?php

namespace App\Controller\Vehicle;

use App\Entity\Vehicle;
use App\Models\JsonError;
use App\Repository\CategoryRepository;
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
 * @Route("/api/vehicle", name="api_vehicle_")
 */
class VehicleController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods={"GET"})
     *
     * @param VehicleRepository $vr
     * @return Response
     */
    public function vehicleList(VehicleRepository $vr): Response
    {
        $user = $this->getUser();
        $result = $vr->findBy(['user' => $user]);
        
        return $this->json(
            $result,
            200,
            [],
            ['groups' => 'get_vehicle']
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
    public function createVehicle(Request $req, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $data = $req->getContent();
        $user = $this->getUser();

        try {
            $newVehicle = $serializer->deserialize($data, Vehicle::class, 'json');
        } catch (Exception $e) {
            return new JsonResponse('JSON invalide : ' . $e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $errors = $validator->validate($newVehicle);

        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, 'Des erreurs de validation ont été trouvées.');
            $myJsonError->setValidationErrors($errors);
            // dd($errors[0]);
            // return new JsonResponse($myJsonError, Response::HTTP_UNPROCESSABLE_ENTITY);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        $newVehicle->setUser($user);

        $em->persist($newVehicle);

        try {
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse('Erreur', Response::HTTP_CONFLICT);
        }

        return $this->json(
            $newVehicle,
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_vehicle']
        );
    }
}