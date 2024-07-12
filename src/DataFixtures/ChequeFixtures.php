<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Cheque;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChequeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $cheque = new Cheque();
            $cheque->setAmount($this->faker->numberBetween(5.00 , 4000.00));
            $cheque->setDateOfCollection($this->faker->datetime());
            $cheque->setQuote($this->faker->sentence());
         
            $cheque->setChequeBox($this->getRandomReference('CHEQUEBOX'));

            $this->addReference('CHEQUE_' . $i, $cheque);


            $entityManager->persist($cheque);
        }
        $entityManager->flush();
    }

    public function getDependencies()
    {
        return [

            ChequeBoxFixtures::class,
            

        ];
    }
}
