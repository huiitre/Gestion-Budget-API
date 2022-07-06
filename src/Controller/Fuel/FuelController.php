<?php

namespace App\Controller\Fuel;

use App\Repository\CategoryRepository;
use App\Repository\FuelRepository;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}