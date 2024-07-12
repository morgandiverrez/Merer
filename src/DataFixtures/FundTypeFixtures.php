<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\FundType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FundTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        $fundType = new FundType();
        $fundType->setName('1 centimes');
        $fundType->setAmount(0.01);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount() , $fundType);
        $manager->persist($fundType);
       

        $fundType = new FundType();
        $fundType->setName('2 centimes');
        $fundType->setAmount(0.02);
        $this->addReference('FUNDTYPE_'.$fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('5 centimes');
        $fundType->setAmount(0.05);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('10 centimes');
        $fundType->setAmount(0.10);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('20 centimes');
        $fundType->setAmount(0.20);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);


        $fundType = new FundType();
        $fundType->setName('50 centimes');
        $fundType->setAmount(0.50);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('1 euro');
        $fundType->setAmount(1.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('2 euros');
        $fundType->setAmount(2.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('5 euros');
        $fundType->setAmount(5.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('10 euros');
        $fundType->setAmount(10.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('20 euros');
        $fundType->setAmount(20.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('50 euros');
        $fundType->setAmount(50.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('100 euros');
        $fundType->setAmount(100.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('200 euros');
        $fundType->setAmount(200.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);

        $fundType = new FundType();
        $fundType->setName('500 euros');
        $fundType->setAmount(500.00);
        $this->addReference('FUNDTYPE_' . $fundType->getAmount(), $fundType);
        $manager->persist($fundType);
       
        $manager->flush();
    }
}
