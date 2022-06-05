<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Subcategory;
use App\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\MySlugger;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\Providers\TransactionProvider;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $connexion;

    private $hasher;

    public function __construct(Connection $connexion, UserPasswordHasherInterface $hasher)
    {
        $this->connexion = $connexion;
        $this->hasher = $hasher;
    }

    private function truncate()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');

        $this->connexion->executeQuery('TRUNCATE TABLE category');
        $this->connexion->executeQuery('TRUNCATE TABLE subcategory');
        $this->connexion->executeQuery('TRUNCATE TABLE transaction');
        $this->connexion->executeQuery('TRUNCATE TABLE user');
    }

    public function load(ObjectManager $manager)
    {
        include('data.php');

        $this->truncate();

        $faker = Faker::create('fr_FR');


        /**
         *! Ajout des utilisateurs
         */
        $allEntityUsers = [];
        $dataUsers = [
            [
                'username' => 'Yanis',
                'mail' => 'a@a.fr',
                'password' => '123456',
            ],
            [
                'username' => 'Audrey',
                'mail' => 'b@b.fr',
                'password' => '123456',
            ],
            [
                'username' => 'huiitre',
                'mail' => 'c@c.fr',
                'password' => '123456',
            ],
        ];
        foreach ($dataUsers as $value) {
            $user = new User();
            $user->setUsername($value['username']);
            $user->setEmail($value['mail']);
            $hashedPassword = $this->hasher->hashPassword(
                $user,
                $value['password']
            );
            $user->setPassword($hashedPassword);

            $allEntityUsers[] = $user;

            $manager->persist($user);
        }

        
        /**
         *! Ajout des catégories
         */
        $allEntitySubcategories = [];
        foreach ($dataCategories as $key => $value) {

            $category = new Category();
            $category->setName($key);
            $now = new DateTimeImmutable('now');
            $category->setCreatedAt($now);

            foreach ($value as $val) {
                $subCategory = new Subcategory();
                $subCategory->setName($val);
                $subCategory->setCreatedAt($now);

                $allEntitySubcategories[] = $subCategory;

                $manager->persist($subCategory);
                $category->addSubcategory($subCategory);
            }

            $manager->persist($category);
        }


        /**
         *! Ajout des transactions
         */

        $transactionProvider = new TransactionProvider();

        //* ajout du salaire à la mano
        $salaire = new Transaction();
        $salaire->setName('Salaire Distrilog');
        $salaire->setWording('DISTRILOG');
        $salaire->setBalance(975.45);
        $salaire->setCreditedAt(DateTimeImmutable::createFromMutable($faker->dateTimeThisMonth()));
        $salaire->setIsFixed(true);
        $salaire->setIsSeen(true);
        $salaire->setIsActive(true);
        $salaire->setSubcategory($allEntitySubcategories[112]);

        $salaire->setUser($allEntityUsers[0]);

        $manager->persist($salaire);

        for ($i = 0; $i < 8; $i++) {

            $transaction = new Transaction();

            $transaction->setName($transactionProvider->getName()[$i]);
            $transaction->setWording($transactionProvider->getWording()[$i]);
            $transaction->setBalance($transactionProvider->getBalance()[$i]);
            $transaction->setDebitedAt(DateTimeImmutable::createFromMutable($faker->dateTimeThisMonth()));
            $transaction->setIsFixed($transactionProvider->getIsFixed()[$i]);
            $transaction->setIsSeen($transactionProvider->getIsSeen()[$i]);
            $transaction->setIsActive($transactionProvider->getIsActive()[$i]);
            $transaction->setSubcategory($allEntitySubcategories[mt_rand(0, 111)]);

            $transaction->setUser($allEntityUsers[mt_rand(0, 2)]);

            $manager->persist($transaction);
        }

        $manager->flush();
    }
}
