<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Seance;
use App\DataFixtures\LieuxFixtures;
use App\DataFixtures\ProfilFixtures;
use App\DataFixtures\FormationFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeanceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $seance = new Seance();
            $seance->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
            $seance->setName('seance' . $i);
            $seance->setDatetime($this->faker->dateTimeBetween('- 1 month', '+ 1 month'));
            $seance->setNombreplace($this->faker->numberBetween(5,25));
            $seance->setFormation($this->getRandomReference('FORMATION'));
            for($k=0;$k<= $this->faker->numberBetween(1, 2); $k++){
                 $seance->addLieux($this->getRandomReference('LIEU'));
            }
            for ($k = 0; $k <= $this->faker->numberBetween(1, 2); $k++){
                $seance->addProfil($this->getRandomReference('PROFIL'));
            }
            $seance->setEvenement($this->getRandomReference('EVENEMENT'));
            $seance->setParcours($this->faker->randomElement($seance->getEvenement()->getParcours(), 1));
            $this->addReference('SEANCE_'.$i, $seance);
            $seance->setVisible(true);
            $manager->persist($seance);
            
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [

            FormationFixtures::class,
            LieuxFixtures::class,
            ProfilFixtures::class,
            EvenementFixtures::class,

        ];
    }
}
