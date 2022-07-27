<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\CategoryProvider;
use App\Entity\Todo;
use App\Entity\Todolist;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

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
            // 'all',
            'todo'
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->truncate();

        $faker = Faker::create('fr_FR');

        $now = new DateTimeImmutable('now');

        $todosNotDone = 0;
        $allTodos = 0;
        $todosDone = 0;

        for ($i = 1; $i < 15; $i++) {
            $date = DateTimeImmutable::createFromMutable($faker->dateTimeBetween(date('2022-01-01'), date('2022-12-12')));

            $list = new Todolist();
            $list->setName($faker->sentence(3));
            $list->setCreatedAt($date);
            $list->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE . mt_rand(1, 14)));
            $list->setUser($this->getReference(UserFixtures::USER_REFERENCE . mt_rand(0, 2)));

            for ($j = 1; $j < mt_rand(5, 15); $j++) {
                $date2 = DateTimeImmutable::createFromMutable($faker->dateTimeBetween(date('2022-01-01'), date('2022-12-12')));

                $todo = new Todo();
                $todo->setName($faker->sentence(mt_rand(2, 10)));
                $todo->setCreatedAt($date2);
                $todo->setIsDone((bool)mt_rand(0, 1));
                $todo->setPercent($todo->isIsDone() == true ? 100 : mt_rand(0, 99));
                $todo->setTodolist($list);

                if ($todo->isIsDone() == false) {
                    $todosNotDone++;
                } else {
                    $todosDone++;
                }

                $allTodos++;
                $manager->persist($todo);
            }
            // (montant partiel / montant total) x 100
            $list->setPercent(($todosDone * 100) / $allTodos);
            $list->setIsDone($list->getPercent() == 100 ? true : false);

            $list->setAllTodos($allTodos);
            $list->setActiveTodos($todosNotDone);
            $list->setDoneTodos($todosDone);

            $manager->persist($list);

            //* on reset les compteurs à zero
            $todosNotDone = 0;
            $allTodos = 0;
            $todosDone = 0;
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}