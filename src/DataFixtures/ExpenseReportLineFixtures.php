<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\ExpenseReport;
use App\Entity\ExpenseReportLine;
use App\DataFixtures\CustomerFixtures;
use App\DataFixtures\ExerciceFixtures;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\TransactionFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ExpenseReportLineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $expenseReportLine = new ExpenseReportLine();
            $expenseReportLine->setDate($this->faker->dateTimeBetween('-6 month', '+6 month'));
            $expenseReportLine->setObject($this->faker->sentence());
            $expenseReportLine->setAmount($this->faker->numberBetween(0.1, 820000));
            $expenseReportLine->setExpenseReport($this->getRandomReference('EXPENSEREPORT'));
            $this->addReference('EXPENSEREPORT_' . $i, $expenseReportLine);


            $manager->persist($expenseReportLine);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ExpenseReportFixtures::class,

        ];
    }
}
