<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Transaction;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $this->faker = Factory::create();

        for ($i = 0; $i <= 5; $i++) {
            $transaction = new Transaction();
            $transaction->setCode($this->faker->numerify('2022######'));
            $transaction->setClosure($this->faker->boolean());
            $transaction->setQuote($this->faker->word());
            
            for ($k = 0; $k < $this->faker->numberbetween(1, 3); $k++) {
                $transaction->addInvoice($this->getRandomReference('INVOICE'));
            }

            for ($k = 0; $k < $this->faker->numberbetween(1, 3); $k++) {
                $transaction->addTransactionLine($this->getRandomReference('TRANSACTIONLINE'));
            }

            $this->addReference('TRANSACTION_' . $i, $transaction);

            $manager->persist($transaction);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TransactionLineFixtures::class,
            InvoiceFixtures::class,
        ];
    }
}