<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Supplier;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SupplierFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $supplier = new Supplier();
            $supplier->setName('Supplier' . $i);
             $supplier->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));
            $supplier->setLocation($this->getRandomReference('LOCATION'));
            $supplier->setAdministrativeIdentifier($this->getRandomReference('ADMINISTRATIVEIDENTIFIER'));
            $this->addReference('SUPPLIER_' . $i, $supplier);
            $manager->persist($supplier);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [

            ChartOfAccountsFixtures::class,
            LocationFixtures::class,
            AdministrativeIdentifierFixtures::class,

        ];
    }
}
