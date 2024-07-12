<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\TransactionLine;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\ChartOfAccountsFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TransactionLineFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {

        $this->faker = Factory::create();

        for ($k = 0; $k <= 5; $k++) {
            $transactionLine = new TransactionLine();
            $transactionLine->setLabel($this->faker->sentence());
            $transactionLine->setDate($this->faker->datetime());
            $transactionLine->setAmount($this->faker->numberBetween(1 , 5000));
            $transactionLine->setUrlProof($this->faker->url());
            $transactionLine->setQuote($this->faker->url());
            $transactionLine->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));
            


            $this->addReference('TRANSACTIONLINE_' . $k, $transactionLine);

            $manager->persist($transactionLine);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ChartOfAccountsFixtures::class,
        ];
    }
}
