<?php

namespace App\Controller\Vehicle;

use App\Repository\CategoryRepository;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/vehicle", name="api_vehicle_")
 */
class VehicleController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods={"GET"})
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
}