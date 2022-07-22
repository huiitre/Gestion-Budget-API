<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\FuelProvider;
use App\Entity\Fuel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;

class FuelFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private $connexion;

    public const FUEL_REFERENCE = 'fuel_';

    public function __construct(Connection $connexion)
    {
        $this->connexion = $connexion;
    }

    public function truncate()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE fuel');
    }

    public static function getGroups(): array
    {
        return [
            'all',
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->truncate();

        $fuelProvider = new FuelProvider();
        
        foreach ($fuelProvider->getFuel() as $key => $value) {
            $fuel = new Fuel();
            $fuel->setName($value);
            $fuel->setUser($this->getReference(UserFixtures::USER_REFERENCE . 0));

            $this->addReference(self::FUEL_REFERENCE . $key, $fuel);
            $manager->persist($fuel);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}