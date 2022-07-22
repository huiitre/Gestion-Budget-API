<?php

namespace App\DataFixtures\Providers;

class UserProvider
{
    private $user = [
        [
            'username' => 'huiitre',
            'mail' => 'a@a.fr',
            'password' => '123456',
        ],
        [
            'username' => 'huiitre2',
            'mail' => 'b@b.fr',
            'password' => '123456',
        ],
        [
            'username' => 'huiitre3',
            'mail' => 'c@c.fr',
            'password' => '123456',
        ],
        [
            'username' => 'huiitre4',
            'mail' => 'd@d.fr',
            'password' => '123456',
        ],
    ];

    /**
     * Get the value of user
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */ 
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}