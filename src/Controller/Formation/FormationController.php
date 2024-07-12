<?php

namespace App\Controller\Formation;

use App\Entity\Formation\Badge;
use App\Entity\Formation\Formation;
use App\Form\Formation\FormationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/catalogue', name: 'catalogue_')]

class FormationController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_USER')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findAll();

        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name'] ){
                $formations = array_intersect($formations, $entityManager->getRepository(Formation::class)->findAllByName( $posts['name']) );
            }
            if ($posts['categorie']) {
                $formations = array_intersect($formations, $entityManager->getRepository(Formation::class)->findAllByCategorie($posts['categorie']));
            }
            if ($posts['opg']) {
                $formations = array_intersect($formations, $entityManager->getRepository(Formation::class)->findAllByOPG( $posts['opg']));
            }
            if ($posts['publicCible']) {
                $formations = array_intersect($formations, $entityManager->getRepository(Formation::class)->findAllByPublicCible( $posts['publicCible']));
            }
            if ($posts['preRequis']) {
                $formations = array_intersect($formations, $entityManager->getRepository(Formation::class)->findAllByPreRequis($posts['preRequis']));
            }
            if ($posts['durationMax']) {
               
                $formations = array_intersect($formations, $entityManager->getRepository(Formation::class)->findAllDurationInferior($posts['durationMax']));
            }
            if ($posts['durationMin']) {
                $formations = array_intersect($formations, $entityManager->getRepository(Formation::class)->findAllDurationSuperior($posts['durationMin']));
            }
            if ($posts['badge']) {
                $badges = $entityManager->getRepository(Badge::class)->findAllByName($posts['badge']);
                $badgeFormations = array();
                foreach( $badges as $badge ){
                    foreach( $badge->getFormations() as $formation){
                       array_push($badgeFormations, $formation); 
                    }
                }    
                $formations = array_intersect($formations, $badgeFormations);
            }
        }

        return $this->render('Formation_/formation/showAll.html.twig', [     
            'formations' => $formations,
        ]);
    }

  

    #[Route('/show/{formationID}', name: 'show')]
    #[IsGranted('ROLE_USER')]
    public function show(EntityManagerInterface $entityManager, $formationID): Response
    {
        
        $formation = $entityManager->getRepository(Formation::class)->findById($formationID)[0];
       
        return $this->render('Formation_/formation/show.html.twig', [
            'formation'=>$formation,
            
        ]);
    }



    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_BF')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
      

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($formation);
            $entityManager->flush();
            return $this->redirectToRoute('catalogue_showAll', []);
            
        }

        return $this->render('Formation_/formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/edit/{formationID}', name: 'edit')]
    #[IsGranted('ROLE_BF')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $formationID): Response
    {
        $formation = $entityManager->getRepository(Formation::class)->findById($formationID)[0];
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($formation);
            $entityManager->flush();
            return $this->redirectToRoute('catalogue_show', [ 'formationID' => $formationID]);
        }

        return $this->render('Formation_/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            
        ]);
    }


    #[Route('/delete/{formationID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $formationID): Response
    {

        $formation = $entityManager->getRepository(Formation::class)->findById($formationID)[0];
        $entityManager->remove($formation);
        $entityManager->flush();
        
        return $this->redirectToRoute('catalogue_showAll', [
        ]);
    }

}
