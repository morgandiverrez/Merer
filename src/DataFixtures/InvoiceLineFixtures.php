<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\InvoiceLine;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InvoiceLineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
       
        for ($i = 0; $i < 100; $i++) {
            $invoiceLine = new InvoiceLine();
            $invoiceLine->setDiscount(($this->faker->numberBetween(5, 100))/100);
            $invoiceLine->setQuote($this->faker->sentence());
            $invoiceLine->setCatalogDiscount($this->getRandomReference('CATALOGDISCOUNT'));
            $invoiceLine->setCatalogService($this->getRandomReference('CATALOGSERVICE'));
            $this->addReference('INVOICELINE_' . $i, $invoiceLine);
            $manager->persist($invoiceLine);
        }
        $manager->flush();
    }


    public function getDependencies()
    {
        return [

          
            CatalogServiceFixtures::class,
            CatalogDiscountFixtures::class,


        ];
    }
}
