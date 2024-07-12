<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Institution;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InstitutionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $institution = new Institution();
            $institution->setHeadquarter(1);
            $institution->setOpen($this->faker->boolean());

            $institution->setAdministrativeIdentifier($this->getRandomReference('ADMINISTRATIVEIDENTIFIER'));
            $institution->setLocation($this->getRandomReference('LOCATION'));
            if($i==0)$institution->setFederation($this->getRandomReference('FEDERATION'));

            $this->addReference('INSTITUTION_' . $i, $institution);


            $entityManager->persist($institution);
        }
        $entityManager->flush();
    }

    public function getDependencies()
    {
        return [

            AdministrativeIdentifierFixtures::class,


        ];
    }
}
