<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Badge;
use App\Entity\Formation;
use App\DataFixtures\BadgeFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FormationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
       

        for ($i = 0; $i<20; $i++){
            $formation = new Formation();
            $formation ->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
            $formation->setName($this->faker -> sentence());
            $formation->setDescription($this->faker->paragraph());
            $formation->setPreRequis($this->faker->sentence());
            $formation->setPublicCible($this->faker->sentence());
            $formation->setDuration($this->faker->dateTime());
            $formation->setOPG($this->faker->sentence());
            $formation->setBadge($this->getRandomReference('BADGE'));
            $this->addReference('FORMATION_'.$i, $formation);
            
            
            $manager->persist($formation);
            $manager->flush(); 
        }
    }

    public function getDependencies()
    {
        return [

            BadgeFixtures::class,
           
        ];
    }
}
