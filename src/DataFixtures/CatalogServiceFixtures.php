<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\CatalogService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CatalogServiceFixtures extends Fixture
{
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $catalogService = new CatalogService();
            $catalogService->setName('catalogService' . $i);
            $catalogService->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
            $catalogService->setDescription($this->faker->sentence(1));
            $catalogService->setAmountHt(floatval($this->faker->numberBetween(5, 100)) / 100);

            $catalogService->setTvaRate(intval($this->faker->randomElement('0' , '5.5' , '10' , '20')));

            $catalogService->setAmountTtc($catalogService->getAmountHt()*$catalogService->getTvaRate());
            $this->addReference('CATALOGSERVICE_' . $i, $catalogService);


            $entityManager->persist($catalogService);
        }

        $catalogService = new CatalogService();
        $catalogService->setName('plastification');
        $catalogService->setCode('plastification');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_21' . $i, $catalogService);
        $entityManager->persist($catalogService);

        $catalogService = new CatalogService();
        $catalogService->setName('A3RVcouleur');
        $catalogService->setCode('A3RVcouleur');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_22' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A3Rcouleur');
        $catalogService->setCode('A3Rcouleur');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_23' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A3RV');
        $catalogService->setCode('A3RV');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_24' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A3R');
        $catalogService->setCode('A3R');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_25' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A4RVcouleur');
        $catalogService->setCode('A4RVcouleur');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_26' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A4Rcouleur');
        $catalogService->setCode('A4Rcouleur');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_27' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A4RV');
        $catalogService->setCode('A4RV');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_28' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A4R');
        $catalogService->setCode('A4R');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_29' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A5RVcouleur');
        $catalogService->setCode('A5RVcouleur');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_30' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A5Rcouleur');
        $catalogService->setCode('A5Rcouleur');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_31', $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A5RV');
        $catalogService->setCode('A5RV');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_32' . $i, $catalogService);
        $entityManager->persist($catalogService);


        $catalogService = new CatalogService();
        $catalogService->setName('A5R');
        $catalogService->setCode('A5R');
        $catalogService->setDescription($this->faker->sentence(1));
        $catalogService->setAmountHt(10);
        $catalogService->setTvaRate(0);
        $catalogService->setAmountTtc($catalogService->getAmountHt() * $catalogService->getTvaRate());
        $this->addReference('CATALOGSERVICE_33' . $i, $catalogService);
        $entityManager->persist($catalogService);

        $entityManager->flush();
    }
}
