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
use App\Entity\Month;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $connexion;
    private $hasher;
    private $slugger;
    private $sluggerInterface;

    public function __construct(Connection $connexion, UserPasswordHasherInterface $hasher, MySlugger $mySlugger, SluggerInterface $sluggerInterface)
    {
        $this->connexion = $connexion;
        $this->hasher = $hasher;
        $this->slugger = $mySlugger;
        $this->sluggerInterface = $sluggerInterface;
    }

    private function truncate()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');

        $this->connexion->executeQuery('TRUNCATE TABLE category');
        $this->connexion->executeQuery('TRUNCATE TABLE subcategory');
        $this->connexion->executeQuery('TRUNCATE TABLE transaction');
        $this->connexion->executeQuery('TRUNCATE TABLE user');
        $this->connexion->executeQuery('TRUNCATE TABLE month');
    }

    public function load(ObjectManager $manager)
    {
        include('data.php');

        $this->truncate();

        $faker = Faker::create('fr_FR');

        $now = new DateTimeImmutable('now');


        /**
         * ! Ajout des mois
         */
        $allEntityMonths = [];

        foreach ($months as $value) {
            $month = new Month();

            $month->setName($value['name']);
            // $month->setSlug($this->slugger->slugify($value['name']));
            $month->setSlug($this->sluggerInterface->slug($value['name']));
            $month->setCode($value['code']);

            $allEntityMonths[] = $month;

            $manager->persist($month);
        }


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
            $user->setName($value['username']);
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
            $category->setSlug($this->slugger->slugify($key));
            $category->setCreatedAt($now);

            foreach ($value as $val) {
                $subCategory = new Subcategory();
                $subCategory->setName($val);
                $subCategory->setSlug($this->slugger->slugify($val));
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
        $salaire->setSlug($this->slugger->slugify('Salaire Distrilog'));
        $salaire->setCreatedAt($now);

        $salaire->setSubcategory($allEntitySubcategories[112]);
        $salaire->setUser($allEntityUsers[0]);
        $salaire->setMonth($allEntityMonths[2]);

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
            $transaction->setSlug($this->slugger->slugify($transactionProvider->getName()[$i]));
            $transaction->setCreatedAt($now);

            $transaction->setSubcategory($allEntitySubcategories[mt_rand(0, 111)]);
            $transaction->setUser($allEntityUsers[mt_rand(0, 2)]);
            $transaction->setMonth($allEntityMonths[2]);

            $manager->persist($transaction);
        }
        $manager->flush();
    }
}
