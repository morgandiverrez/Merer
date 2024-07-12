<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Contact;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ContactFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $this->faker = Factory::create();

        for ($i = 0; $i <= 20; $i++) {
            $contact = new Contact();
            $contact->setName($this->faker->lastName());
            $contact->setLastName($this->faker->lastName());
            $contact->setCivility($this->faker->randomElement(['Mr', 'Mme']));
            $contact->setMail($this->faker->email());
            $contact->setPhone($this->faker->phoneNumber());
            for($k=0 ; $k< $this->faker->numberbetween(1, 3) ; $k++){
                $contact->addCustomer($this->getRandomReference('CUSTOMER'));
             }
             $contact->setLocation($this->getRandomReference('LOCATION'));
        
            $this->addReference('CONTACT_' . $i, $contact);

            $manager->persist($contact);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CustomerFixtures::class,
        ];
    }
}
