<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\CategoryProvider;
use App\Entity\Category;
use App\Entity\Subcategory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
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
        $this->connexion->executeQuery('TRUNCATE TABLE category');
        $this->connexion->executeQuery('TRUNCATE TABLE subcategory');
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

        $now = new DateTimeImmutable('now');

        $categoryProvider = new CategoryProvider();

        $count = 1;
        foreach ($categoryProvider->getCategories() as $key => $value) {
            
            $category = new Category();
            $category->setName($key);
            $category->setSlug($this->slugger->slug($key));
            $category->setCreatedAt($now);
            
            foreach ($value as $val) {
                $subCategory = new Subcategory();
                $subCategory->setName($val);
                $subCategory->setSlug($this->slugger->slug($val));
                $subCategory->setCreatedAt($now);
                
                $this->addReference(self::SUBCATEGORY_REFERENCE . $count, $subCategory);

                $manager->persist($subCategory);
                $category->addSubcategory($subCategory);
                // dump(array_keys($categoryProvider->getCategories()));
                
                $count++;
            }
            $manager->persist($category);
        }
        $manager->flush();
    }
}