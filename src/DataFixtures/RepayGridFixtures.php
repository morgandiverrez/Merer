<?php

namespace App\DataFixtures;

use App\Entity\RepayGrid;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RepayGridFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $caracteristiques =[];
        $travelMeans = ['Véhicule personnelle', 'Véhicule personnelle'];
        $numberPeoples = [1,2,3,4];
        $coef = [1=>1, 2 =>1,75, 3 =>2, 4 =>2.5];
        $cityforBrest = ['Paris' , 'Rennes' , 'Saint-Brieuc' , 'Morlaix' , 'Lannion', 'Quimper', 'Carhaix'];
        $cityforBrestprix = ['Paris'=> 107,'Rennes'=>30,'Saint-Brieuc'=>16,'Morlaix'=>14,'Lannion'=>16,'Quimper'=>16,'Carhaix'=>30];
        foreach ($travelMeans as $travel) {
            foreach ($numberPeoples as $numb) {
                foreach ($caracteristiques as $cara) {
                    foreach ($cityforBrest as $city) {  
                        $repayGrid = new RepayGrid();
                        $repayGrid->setStart('Brest');
                        $repayGrid->setEnd($city);
                        $repayGrid->setTravelMean($travel);
                        $repayGrid->setNumberPeople($numb);
                        $repayGrid->setAmount($cityforBrestprix[$city]*$coef[$numberPeoples]);
                        $repayGrid->setCaracteristique($cara);

                        $manager->persist($repayGrid);
                    }

                    $repayGrid = new RepayGrid();
                    $repayGrid->setStart('Saint Brieuc');
                    $repayGrid->setEnd('Quimper');
                    $repayGrid->setTravelMean($travel);
                    $repayGrid->setNumberPeople($numb);
                    $repayGrid->setAmount(24 * $coef[$numberPeoples]);
                    $repayGrid->setCaracteristique($cara);

                    $manager->persist($repayGrid);

                    $repayGrid = new RepayGrid();
                    $repayGrid->setStart('Saint Brieuc');
                    $repayGrid->setEnd('Lannion');
                    $repayGrid->setTravelMean($travel);
                    $repayGrid->setNumberPeople($numb);
                    $repayGrid->setAmount(5 * $coef[$numberPeoples]);
                    $repayGrid->setCaracteristique($cara);

                    $manager->persist($repayGrid);
                }
            }
        }
        $manager->flush();
    }
}
