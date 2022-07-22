<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\UserProvider;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $connexion;
    private $hasher;

    public const USER_REFERENCE = 'user_';

    public function __construct(Connection $connexion, UserPasswordHasherInterface $hasher)
    {
        $this->connexion = $connexion;
        $this->hasher = $hasher;
    }

    public function truncate()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE user');
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

        $userProvider = new UserProvider();
        
        foreach ($userProvider->getUser() as $key => $value)
        {
            $user = new User();
            $user->setName($value['username']);
            $user->setEmail($value['mail']);
            $pwd = $this->hasher->hashPassword(
                $user,
                $value['password']
            );
            $user->setPassword($pwd);

            $this->addReference(self::USER_REFERENCE . $key, $user);
            $manager->persist($user);
        }
        $manager->flush();
    }
}