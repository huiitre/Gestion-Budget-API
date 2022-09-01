<?php

namespace App\DataFixtures;

use App\Entity\TEssence;
use App\Entity\Transaction;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker\Factory as Faker;
use PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException;

class TransactionFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private $connexion;
    private $slugger;

    public const SUBCATEGORY_REFERENCE = 'subcategory_';

    public function __construct(Connection $connexion, SluggerInterface $slugger)
    {
        $this->connexion = $connexion;
        $this->slugger = $slugger;
    }

    public function truncate()
    {
        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE transaction');
        $this->connexion->executeQuery('TRUNCATE TABLE tessence');
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

        $faker = Faker::create('fr_FR');

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
                $salaire->setSlug($this->slugger->slug('Salaire Distrilog'));
                //? on va venir se fier à ça dorénavent
                $salaire->setCreatedAt($randDate);

                $salaire->setSubcategory($this->getReference(CategoryFixtures::SUBCATEGORY_REFERENCE . 113));
                $salaire->setUser($this->getReference(UserFixtures::USER_REFERENCE . 0));

                //! déprécié
                // $salaire->setMonth($allEntityMonths[2]);

                //? 1 étant un ajout au compte, 2 étant un retrait
                $salaire->setStatus(1);

                $manager->persist($salaire);
            }
        }

        
        //* ajout des dépenses
        for ($i = 0; $i < 5000; $i++) {

            $transaction = new Transaction();

            $date = DateTimeImmutable::createFromMutable($faker->dateTimeBetween(date('2021-01-01'), date('2022-12-12')));

            $transaction->setBalance(mt_rand(-120 * 10, -90 * 10) / 10);
            
            //! déprécié
            // $transaction->setDebitedAt($date);
            
            //! dépréciés
            $transaction->setIsFixed(true);
            $transaction->setIsSeen(true);
            $transaction->setIsActive(true);
            //! --------------------
            
            //? on va venir se fier à ça dorénavent
            $transaction->setCreatedAt($date);
            $transaction->setSubcategory($this->getReference(CategoryFixtures::SUBCATEGORY_REFERENCE . mt_rand(1, 127)));
            $transaction->setUser($this->getReference(UserFixtures::USER_REFERENCE . 0));
            
            //! déprécié
            // $transaction->setMonth($allEntityMonths[2]);
            
            //? 1 étant un ajout au compte, 2 étant un retrait
            $transaction->setStatus(2);
            
            if ($transaction->getSubcategory()->getName() === 'Carburant') {
                
                $transaction->setName('Essence ' . ($i + 1));
                $transaction->setWording('Essence ' . ($i + 1));
                $transaction->setSlug($this->slugger->slug($transaction->getName()));
                
                $essence = new TEssence();
                $essence->setKmTravelled(mt_rand(350, 800));
                $essence->setPriceLiter(mt_rand(1 * 10, 2 * 10) / 10);
                $essence->setTank(45);
                $essence->setVehicle($this->getReference(VehicleFixtures::VEHICLE_REFERENCE . 1));
                $essence->setFuel($this->getReference(FuelFixtures::FUEL_REFERENCE . mt_rand(1, 4)));
                
                $transaction->setTEssence($essence);
                $manager->persist($essence);
            } else {
                $transaction->setName('Transaction ' . ($i + 1));
                $transaction->setWording('Transaction ' . ($i + 1));
            }
            $transaction->setSlug($this->slugger->slug($transaction->getName()));
            
            $manager->persist($transaction);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
            VehicleFixtures::class,
            FuelFixtures::class
        ];
    }
}
