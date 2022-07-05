<?php

namespace App\DataFixtures\Providers;

class TransactionProvider
{
    private $name = [
        'Mc Donald\'s Vienne',
        'Carrefour Vienne',
        'Essence station Vaugneray',
        'Abonnement Spotify',
        'Abonnement Netflix',
        'Amazon redmi note 11',
        'UberEat avec tiff (chamas tacos)',
        'SushiShop Tassin',
        'Pharmacie Grezieu'
    ];

    private $wording = [
        'MC DONALD S 38VIENNE - 020622 CB5256',
        'GAWAVIDIS 38VIENNE - 010622 CB5256',
        'STATION 69VAUGNERAY - 310522 CB5256',
        'PAYPAL LULUXEMBOURG - 280522 CB5256',
        'NETFLIX.COM NL AMSTERDAM - 220522 CB5256 77.99TRY 1 EURO = 16.628999',
        'AMAZON PAYMENTS75PAYLI441535/ - 200522 CB5256',
        'UBER*EATS NL HELP.UBER.CO - 280522 CB5256',
        'sushi tassin 69TASSIN LA DEM - 260522 CB5256',
        'PHIE LA GARENNE69GREZIEU LA V - 210522 CB5256'
    ];

    
    private $dataUsers = [
        [
            'username' => 'huiitre',
            'mail' => 'a@a.fr',
            'password' => '123456',
        ]
    ];

    /* private $balance = [
        -12.95,
        -5.94,
        -32.32,
        -9.99,
        -5,
        -377.05,
        -38.45,
        -31.30,
        -11.05,
        44.54,
        24.54,
        245,

    ];

    private $isFixed = [
        true,
        false
    ];

    private $isSeen = [
        true,
        false
    ];

    private $isActive = [
        true,
        false
    ]; */

    /**
     * Get the value of orderName
     */ 
    public function getWording()
    {
        return $this->wording[array_rand($this->wording)];
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name[array_rand($this->name)];
    }

    /**
     * Get the value of dataUsers
     */ 
    public function getDataUsers()
    {
        return $this->dataUsers;
    }

    /**
     * Set the value of dataUsers
     *
     * @return  self
     */ 
    public function setDataUsers($dataUsers)
    {
        $this->dataUsers = $dataUsers;

        return $this;
    }
}