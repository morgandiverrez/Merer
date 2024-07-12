<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\SeanceProfil;
use App\DataFixtures\ProfilFixtures;
use App\DataFixtures\SeanceFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeanceProfilFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
     

        for ($i = 0; $i < 20; $i++) {
            $seanceProfil = new SeanceProfil();
 
            $seance = $this->getRandomReference('SEANCE');
            $seanceProfil->setSeance($seance);

            $seanceProfil->setHorrodateur($this->faker->dateTime($seanceProfil->getSeance()->getDatetime()));

            $profil = $this->getRandomReference('PROFIL')  ; 
            $seanceProfil->setProfil($profil);
                    
            $seanceProfil->setAttente($this->faker->sentence());            
            $seanceProfil->setLieu($this->getRandomReference('LIEU'));
            
            $this->addReference('SEANCEPROFIL_'.$i, $seanceProfil);
            
            $manager->persist($seanceProfil);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeanceFixtures::class,
            ProfilFixtures::class,

        ];
    }
}
