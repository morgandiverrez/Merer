<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\ChequeBox;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChequeBoxFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $chequeBox = new ChequeBox();
            $chequeBox->setName('ChequeBox'.$i);
            $chequeBox->setDescription($this->faker->sentence());
            $chequeBox->setLastCountDate($this->faker->datetime());
            $chequeBox->setLocation($this->getRandomReference('LOCATION'));
            $chequeBox->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));

            $this->addReference('CHEQUEBOX_' . $i, $chequeBox);


            $entityManager->persist($chequeBox);
        }
        $entityManager->flush();
    }
    
    public function getDependencies()
    {
        return [

            ChartOfAccountsFixtures::class,


        ];
    }
}
