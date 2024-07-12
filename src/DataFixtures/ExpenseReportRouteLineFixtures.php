<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\ExpenseReportRouteLine;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ExpenseReportRouteLineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $expenseReportRouteLine = new ExpenseReportRouteLine();
            $expenseReportRouteLine->setStart($this->faker->word());
            $expenseReportRouteLine->setEnd($this->faker->word());
            $expenseReportRouteLine->setTravelMean($this->faker->word());
            $expenseReportRouteLine->setDate($this->faker->dateTimeBetween('-6 month', '+6 month'));
            $expenseReportRouteLine->setDistance($this->faker->numberBetween(0.1, 820000));
            $expenseReportRouteLine->setAmount($this->faker->numberBetween(0.1, 820000));
            $expenseReportRouteLine->setRepayGrid($this->getRandomReference('REPAYGRID'));
            $expenseReportRouteLine->setExpenseReport($this->getRandomReference('EXPENSEREPORT'));
            $this->addReference('EXPENSEREPORTROUTELINE_' . $i, $expenseReportRouteLine);

            $manager->persist($expenseReportRouteLine);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ExpenseReportFixtures::class,
            RepayGridFixtures::class,
           

        ];
    }
}
