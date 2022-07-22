<?php

namespace App\Purger;

use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\DBAL\Connection;


class Purger implements PurgerInterface
{
    private $connexion;

    public function __construct(Connection $connexion)
    {
        $this->connexion = $connexion;
    }

    public function purge(): void
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');

        $this->connexion->executeQuery('TRUNCATE TABLE fuel');
        $this->connexion->executeQuery('TRUNCATE TABLE vehicle');
        $this->connexion->executeQuery('TRUNCATE TABLE category');
        $this->connexion->executeQuery('TRUNCATE TABLE subcategory');
        $this->connexion->executeQuery('TRUNCATE TABLE transaction');
        $this->connexion->executeQuery('TRUNCATE TABLE tessence');
        $this->connexion->executeQuery('TRUNCATE TABLE user');
        $this->connexion->executeQuery('TRUNCATE TABLE vehicle');
        $this->connexion->executeQuery('TRUNCATE TABLE fuel');
    }
}