<?php

namespace App\DataFixtures\Providers;

class VehicleProvider
{
    private $vehicle = [
        [
            'name' => 'Renault Clio 3',
            'immat' => 'AA385PD'
        ],
        [
            'name' => 'Ford fiesta',
            'immat' => 'CC281MD'
        ],
        [
            'name' => 'vl 3',
            'immat' => 'CB451DY'
        ],
        [
            'name' => 'vl 4',
            'immat' => 'CB451DY'
        ],
            
    ];

    /**
     * Get the value of vehicle
     */ 
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * Set the value of vehicle
     *
     * @return  self
     */ 
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}