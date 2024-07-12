<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\ChartOfAccounts;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChartOfAccountsFixtures extends Fixture 
{
    public function load(ObjectManager $manager): void
    {

        $this->faker = Factory::create();

        for ($k = 0; $k <= 20; $k++) {
            $chartOfAccounts = new ChartOfAccounts();
            $chartOfAccounts->setName($this->faker->word());
            $chartOfAccounts->setMovable($this->faker->boolean());
            $chartOfAccounts->setCode($this->faker->numerify('####'));

            $this->addReference('CHARTOFACCOUNTS_' . $k, $chartOfAccounts);

            $manager->persist($chartOfAccounts);
        }

        $manager->flush();
    }

 
}

