<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\BankAccount;
use App\DataFixtures\LocationFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BankAccountFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $bankAccount = new BankAccount();
            $bankAccount->setAccountNumber($this->faker->numerify('#####'));
            $bankAccount->setRibBankCode($this->faker->numerify('#####'));
            $bankAccount->setRibBranchCode($this->faker->regexify('[0-9]{5}'));
            $bankAccount->setRibAccountNumber($this->faker->numerify('###########'));
            $bankAccount->setRibKey($this->faker->numerify('##'));
            $bankAccount->setIban('FR'.$this->faker->regexify('[0-9]{25}'));
            $bankAccount->setBic($this->faker->regexify('[A-Z]{8}-[0-9]{3}'));
             $bankAccount->setLastCountDate($this->faker->datetime());

            $bankAccount->setChartOfAccounts($this->getRandomReference('CHARTOFACCOUNTS'));

            $bankAccount->setLocation($this->getRandomReference('LOCATION'));
            $this->addReference('BANKACCOUNT_' . $i, $bankAccount);


            $entityManager->persist($bankAccount);
        }
        $entityManager->flush();
    }

    public function getDependencies()
    {
        return [

            LocationFixtures::class,
            ChartOfAccountsFixtures::class,

        ];
    }


}


