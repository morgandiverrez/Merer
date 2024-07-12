<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\FundBox;
use App\Entity\FundType;
use App\Entity\FundTypeFundBox;
use App\DataFixtures\FundBoxFixtures;
use App\DataFixtures\FundTypeFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FundTypeFundBoxFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        
        $fundTypes = $manager->getRepository(FundType::class)->findAll();
        $fundBox = $manager->getRepository(FundBox::class)->findAll();

        $i=0;
        foreach($fundBox as $box){
            foreach( $fundTypes as $type){
                $fundTypeFunBox = new FundTypeFundBox();
                $fundTypeFunBox->setQuantity($this->faker->numberBetween(0, 200));
                $fundTypeFunBox->setFundType($this->getReference('FUNDTYPE_'.$type->getAmount()));
                $fundTypeFunBox->setFundBox($this->getReference('FUNDBOX_'. $box->getName()));

                $this->addReference('FUNDTYPEFUNDBOX_' . $i, $fundTypeFunBox);
                $manager->persist($fundTypeFunBox);
                $i++;
            }
        } 
            $manager->flush();
    }

    public function getDependencies()
    {
        return [

            FundBoxFixtures::class,
           FundTypeFixtures::class,


        ];
    }
}
