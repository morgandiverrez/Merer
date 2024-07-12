<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Event;
use App\DataFixtures\ExerciceFixtures;
use App\DataFixtures\LocationFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\FinancementLineFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $event = new Event();
            $event->setName($this->faker->word());
            $event->setCode($this->faker->numerify('####'));
            $event->setStartDate($this->faker->dateTimeBetween('-6 month', '+6 month'));
            $event->setEndDate(date_add($event->getStartDate() , date_interval_create_from_date_string("3 day")));
            $event->setDescription($this->faker->sentence());
            $event->setAmount($this->faker->numberBetween(0.1 , 820000));
             $event->setExercice($this->getRandomReference('EXERCICE'));
            $event->setLocation($this->getRandomReference('LOCATION'));
            $event->setFinancementLine($this->getRandomReference('FINANCEMENTLINE'));
            $this->addReference('EVENT_' . $i, $event);

            $manager->persist($event);
          
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return [
            FinancementLineFixtures::class,
            ExerciceFixtures::class,
            LocationFixtures::class,
           
        ];
    }
}
