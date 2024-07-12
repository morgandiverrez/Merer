<?php

namespace App\DataFixtures;

use App\Entity\AdministrativeIdentifier;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $customer = new Customer();
            $user = new User();
            $customer->setImpressionAccess($this->faker->boolean());
            $customer->setName('Customer' . $i);
            $customer->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));
            $customer->setLocation($this->getRandomReference('LOCATION'));

            $this->addReference('CUSTOMER_' . $i, $customer);

            $user->setEMail($this->faker->email());
            $user->setCustomer($customer);
            $user->setRoles($this->faker->randomElements(['ROLE_FORMATEURICE', 'ROLE_BF', 'ROLE_USER'], 2));
            $user->setPassword($this->faker->password());
            $this->addReference('USER_' . $i+20, $user);
            $entityManager->persist($user);
            $entityManager->persist($customer);
        }
        $customer = new Customer();
        $customer->setImpressionAccess($this->faker->boolean());
        $customer->setUser($this->getReference('USER_FORMA'));
        $customer->setName('Customer' . $i);
        $customer->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));
        $customer->setLocation($this->getRandomReference('LOCATION'));
        $entityManager->persist($customer);
        $this->addReference('CUSTOMER_FORMA', $customer);

        $customer = new Customer();
        $customer->setImpressionAccess($this->faker->boolean());
        $customer->setUser($this->getReference('USER_FORMATEURICE'));
        $customer->setName('Customer' . $i);
        $customer->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));
        $customer->setLocation($this->getRandomReference('LOCATION'));
        $entityManager->persist($customer);
        $this->addReference('CUSTOMER_FORMATEURICE' , $customer);

        $customer = new Customer();
        $customer->setImpressionAccess($this->faker->boolean());
        $customer->setUser($this->getReference('USER_TRESO'));
        $customer->setName('Customer' . $i);
        $customer->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));
        $customer->setLocation($this->getRandomReference('LOCATION'));
        $entityManager->persist($customer);
        $this->addReference('CUSTOMER_TRESO', $customer);
        $entityManager->persist($customer);

        $customer = new Customer();
        $customer->setImpressionAccess($this->faker->boolean());
        $customer->setUser($this->getReference('USER_PREZ'));
        $customer->setName('Customer' . $i);
        $customer->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));
        $customer->setLocation($this->getRandomReference('LOCATION'));
        $entityManager->persist($customer);
        $this->addReference('CUSTOMER_PREZ', $customer);
 

        $customer = new Customer();
        $customer->setImpressionAccess($this->faker->boolean());
        $customer->setUser($this->getReference('USER_BF'));
        $customer->setName('Customer' . $i);
        $customer->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));
        $customer->setLocation($this->getRandomReference('LOCATION'));
        $entityManager->persist($customer);
        $this->addReference('CUSTOMER_BF', $customer);

        $entityManager->flush();
    }
    
    public function getDependencies()
    {
        return [

            ChartOfAccountsFixtures::class,
            ProfilFixtures::class,
            LocationFixtures::class,


        ];
    }
}
