<?php

namespace App\Controller\Formation;

use App\Entity\Formation\EquipeElu;
use App\Form\Formation\EquipeEluType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


 #[Route('/equipeElu', name: 'equipeElu_')]

class EquipeEluController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $equipeElus = $entityManager->getRepository(EquipeElu::class)->findAll();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {

                $equipeElus = array_intersect($equipeElus, $entityManager->getRepository(EquipeElu::class)->findAllByName($posts['name']));
            }
            if ($posts['categorie']) {
                $equipeElus = array_intersect($equipeElus, $entityManager->getRepository(EquipeElu::class)->findAllByCategorie($posts['categorie']));
            }
            if ($posts['etablissement']) {
                $equipeElus = array_intersect($equipeElus, $entityManager->getRepository(EquipeElu::class)->findAllByEtablissement($posts['etablissement']));
            }
            if ($posts['fedeFi']) {
                $equipeElus = array_intersect($equipeElus, $entityManager->getRepository(EquipeElu::class)->findAllByFedeFi($posts['fedeFi']));
            }
        }
        return $this->render('Formation_/equipe_elu/showAll.html.twig', [
            'equipeElus' => $equipeElus,
            
        ]);
    }
    
    #[Route('/show/{equipeEluID}', name: 'show')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function show(EntityManagerInterface $entityManager,   $equipeEluID): Response
    {
        $equipeElu = $entityManager->getRepository(EquipeElu::class)->findById($equipeEluID)[0];
        
        return $this->render('Formation_/equipe_elu/show.html.twig', [
            'equipeElu' => $equipeElu,
            
        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_BF')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $equipeElu = new EquipeElu();
        $form = $this->createForm(EquipeEluType::class, $equipeElu);
        $form->handleRequest($request);
       

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($equipeElu);
            $entityManager->flush();
            return $this->redirectToRoute('equipeElu_show', ['equipeEluID' => $equipeElu -> getId()]);
        }

        return $this->render('Formation_/equipe_elu/new.html.twig', [
            'equipeElu' => $equipeElu,
            'form' => $form->createView(),
            

        ]);
    }



    #[Route('/edit/{equipeEluID}', name: 'edit')]
    #[IsGranted('ROLE_BF')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $equipeEluID): Response
    {
        $equipeElu = $entityManager->getRepository(EquipeElu::class)->findById($equipeEluID)[0];
        $form = $this->createForm(EquipeEluType::class, $equipeElu);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($equipeElu);
            $entityManager->flush();
            return $this->redirectToRoute('equipeElu_show', ['equipeEluID' => $equipeEluID]);
        }

        return $this->render('Formation_/equipe_elu/edit.html.twig', [
            'equipeElu' => $equipeElu,
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/delete/{equipeEluID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $equipeEluID): Response
    {
        $equipeElu = $entityManager->getRepository(EquipeElu::class)->findById($equipeEluID)[0];
           
        $entityManager->remove($equipeElu);
        $entityManager->flush();
        return $this->redirectToRoute('equipeElu_showAll');
    }

   

    #[Route('/remove/{equipeEluID}', name: 'remove')]
    #[IsGranted('ROLE_ADMIN')]
    public function remove1(EntityManagerInterface $entityManager, $equipeEluID): Response
    {

        $equipeElu = $entityManager->getRepository(EquipeElu::class)->findById($equipeEluID)[0];
        $profils = $equipeElu -> getProfil();
        foreach ($profils as $profil){
            $equipeElu->removeProfil($profil);
        }
        $entityManager->persist($equipeElu);
        $entityManager->flush();

        return $this->redirectToRoute('equipeElu_show',['equipeEluID' => $equipeEluID]);
    }
      
}
