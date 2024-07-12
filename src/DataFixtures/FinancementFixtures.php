<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Financement;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FinancementFixtures extends Fixture 
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $financement = new Financement();
            $financement->setName($this->faker->word());
            $financement->setFinanceur($this->faker->lastName());
            $financement->setAmount($this->faker->numberBetween(100, 100000));
            $financement->setDateVersement($this->faker->datetime());
            $financement->setDateSignature($this->faker->datetime());
            $financement->setFlechee($this->faker->boolean());
            $this->addReference('FINANCEMENT_' . $i, $financement);

            $manager->persist($financement);
        }
        $manager->flush();
    }

  
  
}
