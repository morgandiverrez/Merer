<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\CatalogDiscount;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CatalogDiscountFixtures extends Fixture 
{
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $catalogDiscount = new CatalogDiscount();
            $catalogDiscount->setName('catalogDiscount' . $i);
            $catalogDiscount->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
            $catalogDiscount->setDescription($this->faker->sentence(1));
             $this->addReference('CATALOGDISCOUNT_' . $i, $catalogDiscount);


            $entityManager->persist($catalogDiscount);
        }
        $entityManager->flush();
    }
    

}
