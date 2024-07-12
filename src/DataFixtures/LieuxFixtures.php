<?php

namespace App\DataFixtures;

use Faker\Factory;

use App\Entity\Lieux;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class LieuxFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        for ($i = 0; $i<20; $i++){
            $lieu = new Lieux();
            $lieu ->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
            $lieu->setName('Lieu' . $i);
            $lieu->setSalle($this->faker->regexify('[A-Z]{1}-[0-9]{2}'));
            $lieu->setAdresse($this->faker->sentence());
            $lieu->setCodePostale($this->faker->numerify('#####'));
            $lieu->setVille($this->faker->randomElement(['Brest', 'Quimper', 'Lannion', 'St Brieuc', 'Plouzané', 'Morlaix']));
            $this->addReference('LIEU_' . $i, $lieu);
            $manager->persist($lieu);
            $manager->flush();
        }
        
    }
}
