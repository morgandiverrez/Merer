<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\ExpenseReport;
use App\DataFixtures\CustomerFixtures;
use App\DataFixtures\ExerciceFixtures;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\TransactionFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ExpenseReportFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $expenseReport = new ExpenseReport();
            $expenseReport->setCode($this->faker->numerify('####'));
            $expenseReport->setDate($this->faker->dateTimeBetween('-6 month', '+6 month'));
            $expenseReport->setMotif($this->faker->sentence());
            $expenseReport->setComfirm(true);
            $expenseReport->setExercice($this->getRandomReference('EXERCICE'));
            $expenseReport->setTransaction($this->getRandomReference('TRANSACTION'));
            $expenseReport->setCustomer($this->getRandomReference('CUSTOMER'));
            $this->addReference('EXPENSEREPORT_' . $i, $expenseReport);


            $manager->persist($expenseReport);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TransactionFixtures::class,
            ExerciceFixtures::class,
            CustomerFixtures::class,

        ];
    }
}
