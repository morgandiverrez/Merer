<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\FinancementLine;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\FinancementFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\TransactionLineFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FinancementLineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $financementLine = new FinancementLine();
            $financementLine->setLibellee($this->faker->word());
            $financementLine->setAmount($this->faker->numberBetween(10, 40000));
            $financementLine->setDescription($this->faker->sentence());
            $financementLine->setAmount($this->faker->numberBetween(10, 40000));
            $financementLine->setFinancement($this->getRandomReference('FINANCEMENT'));
            $this->addReference('FINANCEMENTLINE_' . $i, $financementLine);
            
            $manager->persist($financementLine);
        }
        $manager->flush();
    }


    public function getDependencies()
    {
        return [

            FinancementFixtures::class,
            TransactionLineFixtures::class,

        ];
    }
}
