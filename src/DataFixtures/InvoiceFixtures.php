<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\FundBox;
use App\Entity\Invoice;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $invoice = new Invoice();
            $invoice->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
            $invoice->setCreationDate($this->faker->datetime());
            $invoice->setAcquitted($this->faker->boolean());
            $invoice->setReady($this->faker->boolean());
            $invoice->setComfirm($this->faker->boolean());
            $invoice->setAcquitted($this->faker->boolean());
            $invoice->setCredit($this->faker->boolean());
            
            for ($k = 0; $k < $this->faker->numberBetween(1, 5); $k++) {
                 $invoice->addInvoiceLine($this->getRandomReference('INVOICELINE'));
            }
            $invoice->setCustomer(($this->getRandomReference('CUSTOMER')));
            $invoice->setExercice($this->getRandomReference('EXERCICE'));
            $this->addReference('INVOICE_' . $i, $invoice);

            $manager->persist($invoice);
          
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [

            CustomerFixtures::class,
            InvoiceLineFixtures::class,
            ExerciceFixtures::class,


        ];
    }
}
