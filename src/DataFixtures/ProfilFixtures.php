<?php

namespace App\DataFixtures;

use Faker\Factory;

use App\Entity\User;
use App\Entity\Profil;
use App\Entity\EquipeElu;
use App\Entity\Association;

use App\DataFixtures\EquipeEluFixtures;
use App\DataFixtures\AssociationFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $hasher; //  erreur apparait si pas bonne version PHP

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $entityManager): void
    {
        $this->faker = Factory::create();

       
      
        for ($i = 0; $i<20; $i++){
            $profil = new Profil();
            $user = new User();
            $profil ->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
            $profil->setLastName($this->faker -> lastName());
            $profil->setName($this->faker -> lastName());
            $profil->setPronom($this->faker->randomElement(['il', 'iel', 'elle']));
            $profil->setDateOfBirth($this->faker->dateTime());
            $profil->setTelephone($this->faker->phoneNumber());
            for($k=0; $k <= $this->faker->numberBetween(0, 3); $k++){
                 $profil->addEquipeElu($this->getRandomReference('EQUIPEELU'));
            }
             for($k=0; $k <= $this->faker->numberBetween(0, 2); $k++){
                 $profil->addAssociation($this->getRandomReference('ASSOCIATION'));
             }
            $this->addReference('PROFIL_'.$i, $profil);


            $user->setEMail($this->faker->email());
            $user->setProfil($profil);
            $user->setRoles($this->faker->randomElements(['ROLE_FORMATEURICE', 'ROLE_BF', 'ROLE_USER'], 2));
            $user->setPassword($this->faker->password());
            $this->addReference('USER_'.$i, $user);

            $entityManager->persist($profil);
            $entityManager->persist($user);
            
        }

        $profil2 = new Profil();
        $user2 = new User();
        $profil2->setCode('ADMIN');
        $profil2->setLastName('PRESIDENCE');
        $profil2->setName('PAUL');
        $profil2->setPronom($this->faker->randomElement(['il', 'iel', 'elle']));
        $profil2->setDateOfBirth($this->faker->dateTime());
        $profil2->setTelephone($this->faker->phoneNumber());
        $this->addReference('PROFIL_PREZ', $profil2);

        $user2->setEMail('presidence@*****.net');
        $user2->setProfil($profil2);
        $user2->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($user, 'password');
        $user2->setPassword($password);
        $this->addReference('USER_PREZ', $user2);


        $entityManager->persist($profil2);
        $entityManager->persist($user2);

        $profil2a = new Profil();
        $user2a = new User();
        $profil2a->setCode('TRESO');
        $profil2a->setPronom($this->faker->randomElement(['il', 'iel', 'elle']));
        $profil2a->setLastName('TRESO');
        $profil2a->setName('Paul');
        $profil2a->setDateOfBirth($this->faker->dateTime());
        $profil2a->setTelephone($this->faker->phoneNumber());
        $this->addReference('PROFIL_TRESO', $profil2a);

        $user2a->setEMail('treso@*****.net');
        $user2a->setProfil($profil2a);
        $user2a->setRoles(['ROLE_TRESO']);
        $user2a->setPassword($password);
        $this->addReference('USER_TRESO', $user2a);


        $entityManager->persist($profil2a);
        $entityManager->persist($user2a);

        $profil2b = new Profil();
        $user2b = new User();
        $profil2b->setCode('FORMA');
        $profil2b->setLastName('FORMA');
        $profil2b->setName('Paul');
        $profil2b->setPronom($this->faker->randomElement(['il', 'iel', 'elle']));
        $profil2b->setDateOfBirth($this->faker->dateTime());
        $profil2b->setTelephone($this->faker->phoneNumber());
        $this->addReference('PROFIL_FORMA', $profil2b);

        $user2b->setEMail('formation@*****.net');
        $user2b->setProfil($profil2b);
        $user2b->setRoles(['ROLE_FORMA']);
        $user2b->setPassword($password);
        $this->addReference('USER_FORMA', $user2b);


        $entityManager->persist($profil2b);
        $entityManager->persist($user2b);


        $profil3 = new Profil();
        $user3 = new User();
        $profil3->setCode('FORMATEURICE');
        $profil3->setLastName('FORMATEURICE');
        $profil3->setName('LE');
        $profil3->setPronom($this->faker->randomElement(['il', 'iel', 'elle']));
        $profil3->setDateOfBirth($this->faker->dateTime());
        $profil3->setTelephone($this->faker->phoneNumber());
  
        $this->addReference('PROFIL_FORMATEURICE', $profil3);


        $user3->setEMail('formateurice@*****.net');
        $user3->setProfil($profil3);
        $user3->setRoles(['ROLE_FORMATEURICE']);
        $user3->setPassword($password);
        $this->addReference('USER_FORMATEURICE', $user3);

        $entityManager->persist($profil3);
        $entityManager->persist($user3);

        $entityManager->flush();

        $profil4 = new Profil();
        $user4 = new User();
        $profil4->setCode($this->faker->regexify('[A-Z]{2}-[0-9]{2}'));
        $profil4->setLastName('DIVERREZ');
        $profil4->setPronom($this->faker->randomElement(['il', 'iel', 'elle']));
        $profil4->setName('Morgan');
        $profil4->setDateOfBirth($this->faker->dateTime());
        $profil4->setTelephone('0651812671');
       
        $this->addReference('PROFIL_USER', $profil4);


        $user4->setEMail('user@*****.net');
        $user4->setProfil($profil4);
        $user4->setRoles(['ROLE_USER']);
        $user4->setPassword($password);
        $this->addReference('USER_USER', $user4);

        $entityManager->persist($profil4);
        $entityManager->persist($user4);


        $profil5 = new Profil();
        $user5 = new User();
        $profil5->setCode($this->faker->regexify('[A-Z]{3}-[0-9]{2}'));
        $profil5->setLastName('BF');
        $profil5->setName('EQUIPE');
        $profil5->setPronom($this->faker->randomElement(['il', 'iel', 'elle']));
        $profil5->setDateOfBirth($this->faker->dateTime());
        $profil5->setTelephone($this->faker->phoneNumber());
      
        $this->addReference('PROFIL_5b', $profil5);

        $user5->setEMail('bf@*****.net');
        $user5->setProfil($profil5);
        $user5->setRoles(['ROLE_BF']);
        $user5->setPassword($password);
        $this->addReference('USER_BF', $user5);

        $entityManager->persist($profil5);
        $entityManager->persist($user5);


        $entityManager->flush();

        
    }

    public function getDependencies()
    {
        return [

            AssociationFixtures::class,
            EquipeEluFixtures::class,

        ];
    }
}
