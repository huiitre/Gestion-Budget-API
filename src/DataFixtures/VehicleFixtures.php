<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\VehicleProvider;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;

class VehicleFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private $connexion;

    public const VEHICLE_REFERENCE = 'vehicle_';

    public function __construct(Connection $connexion)
    {
        $this->connexion = $connexion;
    }

    public function truncate()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE vehicle');
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

        $VehicleProvider = new VehicleProvider();

        foreach ($VehicleProvider->getVehicle() as $key => $value) {
            $vehicle = new Vehicle();
            $vehicle->setName($value['name']);
            $vehicle->setImmat($value['immat']);
            $vehicle->setUser($this->getReference(UserFixtures::USER_REFERENCE . 0));
            
            $this->addReference(self::VEHICLE_REFERENCE . $key, $vehicle);
            $manager->persist($vehicle);
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