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

class AppFixtures extends Fixture
{
    private $connexion;

    public function __construct(Connection $connexion)
    {
        $this->connexion = $connexion;
    }

    private function truncate()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');

        $this->connexion->executeQuery('TRUNCATE TABLE category');
        $this->connexion->executeQuery('TRUNCATE TABLE subcategory');
        $this->connexion->executeQuery('TRUNCATE TABLE transaction');
    }

    public function load(ObjectManager $manager)
    {
        include('data.php');

        $this->truncate();

        $faker = Faker::create('fr_FR');

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

            $manager->persist($transaction);
        }

        $manager->flush();
    }
}