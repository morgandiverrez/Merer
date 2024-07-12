<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Exercice;
use Faker\Factory;

class ExerciceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
          for ($i = 2020; $i<=2023; $i++){
            $exercice = new Exercice();
            $exercice->setAnnee($i);
            $this->addReference('EXERCICE_'.$i, $exercice);
             $manager->persist($exercice);
           
        }

        $manager->flush();
    }
}
