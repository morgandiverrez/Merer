<?php

namespace App\DataFixtures;

use DateInterval;
use Faker\Factory;
use App\Entity\Evenement;
use App\DataFixtures\LieuxFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EvenementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $this->faker = Factory::create();
        
        for ($k=0; $k<=5; $k++){
            $evenement = new Evenement();
            $evenement->setName($this->faker->word());
            $evenement->setURL($this->faker->url());
            $evenement->setParcours($this->faker->randomElements([1,2,3,4,5],3));
            $evenement->setDescription($this->faker->sentence());
            $evenement->setDateDebut($this->faker->dateTimeBetween('-1 month', '+1 month'));
            $evenement->setDateFin(date_add($evenement->getDateDebut() , date_interval_create_from_date_string("3 day")));
            $evenement->setCovoiturage($this->faker->boolean());
            $evenement->setModePaiement($this->faker->words());
            $evenement->setAutorisationPhoto($this->faker->boolean());
            $evenement->setParcoursObligatoire($this->faker->boolean());
            $evenement->setVisible(true);
            $evenement->setDateFinInscription($evenement->getDateDebut());
            $evenement->setLieu($this->getRandomReference('LIEU'));
            $evenement->setNombrePlace($this->faker->numberBetween(40,150));
            $this->addReference('EVENEMENT_' . $k, $evenement);

            $manager->persist($evenement);
        }
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LieuxFixtures::class,
        ];
    }
}
