<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\PaymentDeadline;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PaymentDeadlineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $paymentDeadline = new PaymentDeadline();
            $paymentDeadline->setExpectedPaymentDate($this->faker->datetime());
            $paymentDeadline->setExpectedAmount($this->faker->numberBetween(1 , 10000));
            $paymentDeadline->setExpectedMeans($this->faker->randomElement(['Chèque', 'Espèce', 'LyfPay', 'Virement']));
            $paymentDeadline->setActualPaymentDate($this->faker->datetime());
            $paymentDeadline->setActualAmount($this->faker->numberBetween(1, 10000));
            $paymentDeadline->setActualMeans($this->faker->randomElement(['Chèque', 'Espèce', 'LyfPay', 'Virement']));
            $paymentDeadline->setInvoice($this->getRandomReference('INVOICE'));
            $this->addReference('PAYMENTDEADLINE_' . $i, $paymentDeadline);
            $manager->persist($paymentDeadline);

        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [

            InvoiceFixtures::class,
            
        ];
    }
}
