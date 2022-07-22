<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;

class TodolistFixtures extends Fixture implements FixtureGroupInterface
{
    private $connexion;
    private $hasher;

    public const USER_REFERENCE = 'user_';

    public function __construct(Connection $connexion)
    {
        $this->connexion = $connexion;
    }

    public function truncate()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE todolist');
        $this->connexion->executeQuery('TRUNCATE TABLE todo');
    }

    public static function getGroups(): array
    {
        return [
            'all',
            'todo'
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->truncate();

        

        $manager->flush();
    }
}