<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Federation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
 
class FederationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        $federation = new Federation();
        $federation->setSocialReason('******');
        $federation->setStatutoryObject($this->faker->sentence());
        $federation->setCreationDate($this->faker->datetime());
        $federation->setRepresentedBy('Baptiste LE MASSON');
        $federation->setRna($this->faker->regexify('[0-9]{15}'));
        $federation->setVatNumber($this->faker->regexify('[0-9]{15}'));
        $federation->setRna($this->faker->regexify('[A-Z]{1}'));
        $federation->setCurrency("e");

        $this->addReference('FEDERATION_', $federation);
        $manager->persist($federation);
        $manager->flush();
  
    }

}
