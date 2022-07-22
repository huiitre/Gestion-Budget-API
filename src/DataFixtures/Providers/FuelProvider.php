<?php

namespace App\DataFixtures\Providers;

class FuelProvider
{
    private $fuel = [
        'Gazole',
        'Superéthanol E85',
        'SP95-E10',
        'SP95',
        'SP98'
    ];

    /**
     * Get the value of fuel
     */ 
    public function getFuel()
    {
        return $this->fuel;
    }

    /**
     * Set the value of fuel
     *
     * @return  self
     */ 
    public function setFuel($fuel)
    {
        $this->fuel = $fuel;

        return $this;
    }
}