<?php

namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use App\Entity\AdministrativeIdentifier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AdministrativeIdentifierFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $administrativeIdentifier = new AdministrativeIdentifier();
            $administrativeIdentifier->setSiret($this->faker->numerify('###############'));
            $administrativeIdentifier->setAPE($this->faker->regexify('[A-Z]{3}'));
            $administrativeIdentifier->addCustomer($this->getRandomReference('CUSTOMER'));

            $this->addReference('ADMINISTRATIVEIDENTIFIER_' . $i, $administrativeIdentifier);

            
            $entityManager->persist($administrativeIdentifier);
        }
        $entityManager->flush();
    }

    public function getDependencies()
    {
        return [
            CustomerFixtures::class,
        ];
    }
}
