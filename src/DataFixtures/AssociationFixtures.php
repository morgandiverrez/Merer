<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Association;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AssociationFixtures extends Fixture
{
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        
        for ($i = 0; $i < 20; $i++) {
            $association = new Association();
            $association->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
            $association->setImage($this->faker->mimeType());
            $association->setName('Association' . $i);
            $association->setSigle($this->faker->regexify('[A-Z]{6}'));
            $association->setDescription($this->faker->paragraph());
            $association->setFedeFilliere($this->faker->randomElement(['ARES', 'ANEMF', 'AFNEUS', 'BNEI', 'FNAEL', 'UNECD']));
            $association->setFedeTerritoire('******');
            $association->setLocal($this->faker->regexify('[A-Z]{1}-[0-9]{2}'));
            $association->setAdresseMail($this->faker->email());
            $association->setDateElection($this->faker->dateTime());
            $this->addReference('ASSOCIATION_'.$i, $association);

           
            $entityManager->persist($association);
            
        }

        $entityManager->flush();
    }
}
