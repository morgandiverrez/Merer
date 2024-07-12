<?php

namespace App\Controller\Formation;

use App\Entity\User;
use App\Entity\Formation\Badge;
use App\Entity\Formation\Profil;
use App\Form\UserType;
use App\Entity\Comptability\Customer;
use App\Form\Formation\ProfilType;
use App\Entity\Formation\EquipeElu;
use App\Entity\Formation\Association;
use App\Controller\Comptability\InvoiceController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/account', name: 'profil_')]
class ProfilController extends AbstractController
{
    #[Route('/showAll', name: 'showAll')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $profils = $entityManager->getRepository(Profil::class)->findAll();

        if ($request->isMethod('post')) {
            $posts = $request->request->all();

            if ($posts['name']) {
                $profils = array_intersect($profils, $entityManager->getRepository(Profil::class)->findAllByName($posts['name']));
            }
            if ($posts['last_name']) {
                $profils = array_intersect($profils, $entityManager->getRepository(Profil::class)->findAllByLastName($posts['last_name']));
            }
         
            if ($posts['DOBmin']) {
                $profils = array_intersect($profils, $entityManager->getRepository(Profil::class)->findAllDobSuperior($posts['DOBmin']));
            }
            if ($posts['DOBmax']) {
                $profils = array_intersect($profils, $entityManager->getRepository(Profil::class)->findAllDobInferior($posts['DOBmax']));
            }
            if ($posts['badge']) {
                $badges = $entityManager->getRepository(Badge::class)->findAllByName($posts['badge']);
                $badgeProfils = array();
                foreach ($badges as $badge) {
                    foreach ($badge->getProfil() as $profil) {
                        array_push($badgeProfils, $profil);
                    }
                }
                $profils = array_intersect($profils, $badgeProfils);
            }
            if ($posts['association']) {
                $associations = $entityManager->getRepository(Association::class)->findAllByName($posts['association']);
                $associationProfils = array();
                foreach ($associations as $association) {
                    foreach ($association->getProfil() as $profil) {
                        array_push($associationProfils, $profil);
                    }
                }
                $profils = array_intersect($profils, $associationProfils);
            }
            if ($posts['equipeElu']) {
                $equipeElus = $entityManager->getRepository(EquipeElu::class)->findAllByName($posts['equipeElu']);
                $equipeEluProfils = array();
                foreach ($equipeElus as $equipeElu) {
                    foreach ($equipeElu->getProfil() as $profil) {
                        array_push($equipeEluProfils, $profil);
                    }
                }
                $profils = array_intersect($profils, $equipeEluProfils);
            }
        }

        return $this->render('Formation_/profil/showAll.html.twig', [
            'profils' => $profils,
            
        ]);
    }

    #[Route('/', name: 'show')] // afficheur profil perso pour user
    #[IsGranted('ROLE_USER')] 
    public function show(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $profil = $user->getProfil();

        $customer = $user->getCustomer();
        $profilComplet = false;
        
        if($profil ){
            if( $profil->getName() and      $profil->getLastName() and 
                                            $profil->getDateOfBirth() and 
                                            $profil->getTelephone() )
            {
                $profilComplet = true;
            }
        }
        
      
       if(! $this->isGranted("ROLE_TRESO", "ROLE_ADMIN", "ROLE_FORMA")){
            if(! $customer and ! $profilComplet  ) {
                 return $this->redirectToRoute('profil_edit', []);
            }
        }
        $totals = array();
        if($customer){
            foreach ($customer->getInvoices() as $invoice) {
                array_push($totals, (new InvoiceController)->invoiceTotale($invoice));
            }
        }
        return $this->render('Formation_/profil/show.html.twig', [       
            'totals' => $totals,    
        ]);
    }


    #[Route('/show/{profilID}', name: 'showForFormateurice')]
    #[IsGranted('ROLE_FORMATEURICE')] 
    public function showForFormateurice(EntityManagerInterface $entityManager, $profilID): Response
    {
        
        $profil = $entityManager->getRepository( Profil::class)->findByID($profilID)[0];
        return $this->render('Formation_/profil/showForFormateurice.html.twig', [
            'profil' => $profil,
        ]);
    }

   


    #[Route('/edit/{profilID}', name: 'editByAdmin')]
    #[IsGranted('ROLE_BF')]
    public function editByAdmin(EntityManagerInterface $entityManager, Request $request, $profilID): Response
    {
        $profil = $entityManager->getRepository(Profil::class)->findByID($profilID)[0];
        $form = $this->createForm(ProfilType::class, $profil);

        $equipeElus = $profil->getEquipeElu();
        foreach ($equipeElus as $equipe) {
            $profil->removeEquipeElu($equipe);
        }
        $associations = $profil->getAssociation();
        foreach ($associations as $asso) {
            $asso->removeProfil($profil);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $profil->setLastName(mb_strtoupper($profil->getLastName()));
            $profil->setName(mb_convert_case($profil->getName(), MB_CASE_TITLE , "UTF-8"));

            $go = true; $i = 0;
            while ($go) {
                if (isset($form->get('equipeElu')->getData()[$i])) {
                    $nameEquipeElu = $form->get('equipeElu')->getData()[$i];
                    $equipeElu = $entityManager->getRepository(EquipeElu::class)->findByName(strval($nameEquipeElu))[0];
                    $equipeElu->addProfil($profil);
                    $entityManager->persist($profil);
                    
                    $i++;
                } else {
                    $go = false;
                }
            }
            $go = true;
            $i = 0;
            while ($go) {
                if (isset($form->get('association')->getData()[$i])) {
                    $nameAssociation = $form->get('association')->getData()[$i];
                    $association = $entityManager->getRepository(Association::class)->findBySigle(strval($nameAssociation))[0];
                    $association->addProfil($profil);
                    $entityManager->persist($profil);
                    $i++;
                } else {
                    $go = false;
                }
            }

            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('profil_showForFormateurice', ['profilID' => $profil->getID()]);
        }

        return $this->render('Formation_/profil/edit.html.twig', [
            'profil' => $profil,
            'form' => $form->createView(),
            
        ]);
    }



    #[Route('/edit', name: 'edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $profils = $entityManager-> getRepository(Profil::class)->findAll();
        foreach ($profils as $testProfil ){
            if ($testProfil->getUser() == $user){
                $profil = $testProfil;
            }
        }
        if( ! isset($profil)) $profil = new Profil();

         $equipeElus = $profil->getEquipeElu();
        foreach ($equipeElus as $equipe) {
            $profil->removeEquipeElu($equipe);
        }
        $associations = $profil->getAssociation();
        foreach ($associations as $asso) {
            $asso->removeProfil($profil);
        }

        $form = $this->createForm(ProfilType::class, $profil);

     
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $profil->setLastName(mb_strtoupper($profil->getLastName()));
            $profil->setName(mb_convert_case($profil->getName(), MB_CASE_TITLE, "UTF-8"));

            $go = true; $i = 0;
            while ($go) {
                if (isset($form->get('equipeElu')->getData()[$i])) {
                    $nameEquipeElu = $form->get('equipeElu')->getData()[$i];
                    $equipeElu = $entityManager->getRepository(EquipeElu::class)->findByName(strval($nameEquipeElu))[0];
                    $equipeElu->addProfil($profil);
                    $entityManager->persist($profil);

                    $i++;
                } else {
                    $go = false;
                }
            }

            $go = true;
            $i = 0;
            while ($go) {
                if (isset($form->get('association')->getData()[$i])) {
                    $nameAssociation = $form->get('association')->getData()[$i];
                    $association = $entityManager->getRepository(Association::class)->findBySigle(strval($nameAssociation))[0];
                    $association->addProfil($profil);
                    $entityManager->persist($profil);
                    $i++;
                } else {
                    $go = false;
                }
            }
            $profil->setUser($user);
            $entityManager->persist($profil);
             $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profil_show');
        }

        return $this->render('Formation_/profil/edit.html.twig', [
            'form' => $form->createView(),
            'profil' => $profil,
        ]);
    }

    #[Route('/delete/{profilID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $profilID): Response
    {

        $profil = $entityManager->getRepository(Profil::class)->findById($profilID)[0];
        $entityManager->remove($profil);
        $entityManager->flush();


        return $this->redirectToRoute('profil_showAll', []);
    }


    #[Route('/show/seances/{profilID}', name: 'seances')]
    #[IsGranted('ROLE_USER')]
    public function listeFormationsSuivies(EntityManagerInterface $entityManager, $profilID): Response
    {
        
        $profil = $entityManager->getRepository(Profil::class)->findById($profilID)[0];
        $inscriptions = $profil-> getSeanceProfil();


        return $this->render('Formation_/profil/listeFormationSuivie.html.twig', [
            'profil' => $profil,
            'inscriptions' => $inscriptions,
        ]);
    }


  
    #[Route('/showFormateurice', name: 'formateurice')]
    #[IsGranted('ROLE_BF')]
    public function formateurice(EntityManagerInterface $entityManager): Response
    {

        $listeFormateurice = $entityManager->getRepository(User::class)->findAllUserWithProfilAndFormateurice();
 
        return $this->render('Formation_/profil/ListeFormateurice.html.twig', [
            'users' => $listeFormateurice,
            
        ]);
    }

    #[Route('/roles', name: 'roles')]
    #[IsGranted('ROLE_FORMA')]
    public function roles(EntityManagerInterface $entityManager, Request $request): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        foreach($users as $user){
            foreach(["ROLE_ADMIN", "ROLE_TRESO", "ROLE_FORMA"] as $role){
                if(in_array($role, $user->getRoles())){
                    unset($users[array_search($user, $users)]);
       
                }
            }
            if($user->getProfil() == null ){
                unset($users[array_search($user, $users)]);
            }
        }
        if ($request->isMethod('post')) {
            $posts = $request->request->all();

            foreach( $users as $user){
                
                if(isset($posts[$user->getId() . '_bf'])){
                        $user->setRoles(["ROLE_BF"]);
                }       
                elseif(isset($posts[$user->getId() . '_formateurice'])){
                                $user->setRoles(["ROLE_FORMATEURICE"]);           
                }
                else{ 
                    $user->setRoles([]);
                } 
            }   
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profil_showAll');
        }


        return $this->render('Formation_/profil/gestionRoles.html.twig', [
            'users' => $users,  

        ]);
    }

    
}