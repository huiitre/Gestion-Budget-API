<?php

namespace App\DataFixtures\Providers;

class EssenceProvider
{   
    private $dataVehicles = [
        [
            'name' => 'Renault Clio 3 - 1.2 TCE 100 - 2009',
            'immat' => 'AA385PD'
        ]
            
    ];
    
    private $dataFuels = [
        'Gazole',
        'Superéthanol E85',
        'SP95-E10',
        'SP95',
        'SP98'
    ];
    
    /* $months = [
        [
            'name' => 'Janvier',
            'code' => 1
        ],
        [
            'name' => 'Février',
            'code' => 2
        ],
        [
            'name' => 'Mars',
            'code' => 3
        ],
        [
            'name' => 'Avril',
            'code' => 4
        ],
        [
            'name' => 'Mai',
            'code' => 5
        ],
        [
            'name' => 'Juin',
            'code' => 6
        ],
        [
            'name' => 'Juillet',
            'code' => 7
        ],
        [
            'name' => 'Août',
            'code' => 8
        ],
        [
            'name' => 'Septembre',
            'code' => 9
        ],
        [
            'name' => 'Octobre',
            'code' => 10
        ],
        [
            'name' => 'Novembre',
            'code' => 11
        ],
        [
            'name' => 'Décembre',
            'code' => 12
        ],
    ]; */

    /**
     * Get the value of dataVehicles
     */ 
    public function getDataVehicles()
    {
        return $this->dataVehicles;
    }

    /**
     * Set the value of dataVehicles
     *
     * @return  self
     */ 
    public function setDataVehicles($dataVehicles)
    {
        $this->dataVehicles = $dataVehicles;

        return $this;
    }

    /**
     * Get the value of dataFuels
     */ 
    public function getDataFuels()
    {
        return $this->dataFuels;
    }

    /**
     * Set the value of dataFuels
     *
     * @return  self
     */ 
    public function setDataFuels($dataFuels)
    {
        $this->dataFuels = $dataFuels;

        return $this;
    }
}
