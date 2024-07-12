<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Location;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class LocationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $location = new Location();
            $location->setName('location'.$i);
            $location->setAdress($this->faker->address());
            $location->setZipCode($this->faker->numerify('#####'));
            $location->setCity($this->faker->word());

            $this->addReference('LOCATION_' . $i, $location);

            $manager->persist($location);
        }

        $manager->flush();
    }



}
