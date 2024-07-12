<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Demande;
use App\Entity\EquipeElu;
use App\Entity\Association;
use App\DataFixtures\ProfilFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class DemandeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
           $demande = new demande();
           $demande->setName($this->faker->lastName());
            $demande->setProfil($this->getRandomReference('PROFIL'));
            $demande->addAssociation($this->getRandomReference('ASSOCIATION'));
            $demande->addEquipeElu($this->getRandomReference('EQUIPEELU'));
            $demande->setDateDebut($this->faker->dateTimeBetween('-6 month', '+1 month'));
            $demande->setDateFin(date_add($demande->getDateDebut(), date_interval_create_from_date_string("+2 day")));
            $demande->setNombrePersonne($this->faker->numberBetween(5, 20));
            $demande->addFormation($this->getRandomReference('FORMATION'));
            $demande->setDoubleMaillage($this->faker->boolean());

            $this->addReference('DEMANDE_' . $i, $demande);


            $manager->persist($demande);
        
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProfilFixtures::class,
            EquipeEluFixtures::class,
            FormationFixtures::class,
            AssociationFixtures::class,
        ];
    }
}
