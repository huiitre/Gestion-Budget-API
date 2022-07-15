<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\CategoryProvider;
use App\DataFixtures\Providers\EssenceProvider;
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
use App\Entity\Fuel;
use App\Entity\TEssence;
use App\Entity\User;
use App\Entity\Vehicle;

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
    
    public function load(ObjectManager $manager)
    {
        $this->truncate();

        $faker = Faker::create('fr_FR');

        $now = new DateTimeImmutable('now');

        $tp = new TransactionProvider();
        $ep = new EssenceProvider();
        $cp = new CategoryProvider();


        /**
         * ! Ajout des mois
         */
        /* $allEntityMonths = [];

        foreach ($months as $value) {
            $month = new Month();

            $month->setName($value['name']);
            // $month->setSlug($this->slugger->slugify($value['name']));
            $month->setSlug($this->sluggerInterface->slug($value['name']));
            $month->setCode($value['code']);

            $allEntityMonths[] = $month;

            $manager->persist($month);
        } */

        /**
         *! Ajout des utilisateurs
         */
        $allEntityUsers = [];
        foreach ($tp->getDataUsers() as $value) {
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
         *! Ajout des véhicules
         */
        $allEntityVehicles = [];
        foreach ($ep->getDataVehicles() as $key => $value) {
            $vehicle = new Vehicle();
            $vehicle->setName($value['name']);
            $vehicle->setImmat($value['immat']);
            $vehicle->setUser($allEntityUsers[0]);
            $allEntityVehicles[] = $vehicle;
            $manager->persist($vehicle);
        }


        /**
         *! Ajout des carburants
         */
        $allEntityFuels = [];
        foreach ($ep->getDataFuels() as $value) {
            $fuel = new Fuel();
            $fuel->setName($value);
            $fuel->setUser($allEntityUsers[0]);
            $allEntityFuels[] = $fuel;
            $manager->persist($fuel);
        }


        /**
         *! Ajout des catégories
         */
        // $cat = new CategoryProvider();
        $allEntitySubcategories = [];
        foreach ($cp->getDataCategories() as $key => $value) {

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
        //* ajout du salaire par mois
        for ($y = 2019; $y <= 2022; $y++) {
            for ($m = 1; $m <= 12; $m++) {
                $salaire = new Transaction();
                $salaire->setName('Salaire Distrilog');
                $salaire->setWording('DISTRILOG');
                $salaire->setBalance(mt_rand(1800.45, 2145.85));
                $randDate = new DateTimeImmutable($y . '-' . $m . '-' . mt_rand(1, 5));
                //! déprécié
                // $salaire->setCreditedAt($randDate);
                $salaire->setIsFixed(true);
                $salaire->setIsSeen(true);
                $salaire->setIsActive(true);
                $salaire->setSlug($this->slugger->slugify('Salaire Distrilog'));
                //? on va venir se fier à ça dorénavent
                $salaire->setCreatedAt($randDate);

                $salaire->setSubcategory($allEntitySubcategories[112]);
                $salaire->setUser($allEntityUsers[0]);
                //! déprécié
                // $salaire->setMonth($allEntityMonths[2]);
                //? 1 étant un ajout au compte, 2 étant un retrait
                $salaire->setStatus(1);

                $manager->persist($salaire);
            }
        }


        //* ajout des dépenses
        for ($i = 0; $i < 6; $i++) {

            $transaction = new Transaction();

            $date = DateTimeImmutable::createFromMutable($faker->dateTimeBetween(date('2022-07-01'), date('2022-07-31')));

            $transaction->setName($tp->getName());
            $transaction->setWording($tp->getWording());
            $transaction->setBalance(mt_rand(-849 * 10, -5 * 10) / 10);
            //! déprécié
            // $transaction->setDebitedAt($date);
            //! dépréciés
            $transaction->setIsFixed(true);
            $transaction->setIsSeen(true);
            $transaction->setIsActive(true);
            //! --------------------
            $transaction->setSlug($this->slugger->slugify($transaction->getName()));
            //? on va venir se fier à ça dorénavent
            $transaction->setCreatedAt($date);

            // $transaction->setSubcategory($allEntitySubcategories[mt_rand(0, 111)]);
            $transaction->setSubcategory($allEntitySubcategories[39]);
            $transaction->setUser($allEntityUsers[0]);
            //! déprécié
            // $transaction->setMonth($allEntityMonths[2]);
            //? 1 étant un ajout au compte, 2 étant un retrait
            $transaction->setStatus(2);

            if ($transaction->getSubcategory()->getName() === 'Carburant') {
                $essence = new TEssence();
                $essence->setKmTravelled(mt_rand(400, 600));
                $essence->setPriceLiter(mt_rand(1 * 10, 2 * 10) / 10);
                $essence->setTank(45);
                $essence->setVehicle($allEntityVehicles[0]);
                $essence->setFuel($allEntityFuels[2]);
                
                $transaction->setTEssence($essence);
                $manager->persist($essence);
            }

            $manager->persist($transaction);
        }
        $manager->flush();
    }
}
