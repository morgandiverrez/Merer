<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\BP;
use Faker\Factory;

class BPFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $bp = new BP();
            $bp->setCategorie($this->faker->word());
            $bp->setDesignation($this->faker->sentence());
            $bp->setExpectedAmount(($this->faker->numberBetween(-1000, 1000)));
            $bp->setReallocateAmount($this->faker->numberBetween(5, 100));
            $bp->setExercice($this->getRandomReference('EXERCICE'));
            $this->addReference('BP_' . $i, $bp);

            $manager->persist($bp);
          
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return [

            ExerciceFixtures::class,
           
        ];
    }
}
