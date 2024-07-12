<?php

namespace App\Controller\Formation;

use App\Entity\Formation\User;
use App\Entity\Formation\Profil;
use App\Entity\Formation\Association;
use App\Form\Formation\AssociationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/association', name: 'association_')]

class AssociationController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $associations = $entityManager->getRepository(Association::class)->findAll();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $associations = array_intersect($associations, $entityManager->getRepository(Association::class)->findAllByName($posts['name']));
            }
            if ($posts['categorie']) {
                $associations = array_intersect($associations, $entityManager->getRepository(Association::class)->findAllByCategorie($posts['categorie']));
            }
            if ($posts['fedefi']) {
                $associations = array_intersect($associations, $entityManager->getRepository(Association::class)->findAllByFedeFi($posts['fedefi']));
            }
        }
        return $this->render('Formation_/association/showAll.html.twig', [
            'associations' => $associations,
           
        ]);
    }

    #[Route('/show/{associationID}', name: 'show')]
     #[IsGranted('ROLE_FORMATEURICE')]
    public function show(EntityManagerInterface $entityManager, $associationID): Response
    {
        // find renvoi tjr un array (tableau), donc faut mettre [0] pour enlever l'array, si on veut plus d'une valeur s'il y en a, on met pas ou [nombre]
        $association = $entityManager->getRepository(Association::class)->findById($associationID)[0];
       
        return $this->render('Formation_/association/show.html.twig', [
            'association' => $association,
               
        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_BF')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $association = new Association();
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             $logoUpload=$form->get('image')->getData();
             $sigle = $form->get('sigle')->getData();
             $sigleMajuscule = strtoupper($sigle);

            if($logoUpload){
                $logoFileSigle = 'logo_'. $sigleMajuscule . '.' . $logoUpload->guessExtension();
                $association->setImage('build/associationFormation//' . $logoFileSigle);
                try {
                    $logoUpload->move(
                        'build/association',
                        $logoFileSigle
                    );
                } catch (FileException $e) {
                    
                }              
            }

            $entityManager->persist($association);
            $entityManager->flush();
            return $this->redirectToRoute('association_show', ['associationID' => $association -> getId()]);
        }

        return $this->render('Formation_/association/new.html.twig', [
            'association' => $association,
            'form' => $form->createView(),
            
        ]);
    }



    #[Route('/edit/{associationID}', name: 'edit')]
    #[IsGranted('ROLE_BF')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $associationID): Response
    {
        $association = $entityManager->getRepository(Association::class)->findById($associationID)[0];
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $logoUpload = $form->get('image')->getData();
            $sigle = $form->get('sigle')->getData();
            $sigleMajuscule = strtoupper($sigle);
            
            if ($logoUpload) {
                $logoFileSigle = 'logo_' . $sigleMajuscule . '.' . $logoUpload->guessExtension();
                $association->setImage('build/associationFormation//' . $logoFileSigle);
                try {
                    $logoUpload->move(
                        'build/association',
                        $logoFileSigle
                    );
                } catch (FileException $e) {
                }
            }

            $entityManager->persist($association);
            $entityManager->flush();
            return $this->redirectToRoute('association_show', ['associationID' => $associationID]);
        }

        return $this->render('Formation_/association/edit.html.twig', [
            'association' => $association,
            'form' => $form->createView(),
            
        ]);
    }



    #[Route('/delete/{associationID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $associationID): Response
    {

        $association = $entityManager->getRepository(Association::class)->findById($associationID)[0];
        $entityManager->remove($association);
        $entityManager->flush();

        return $this->redirectToRoute('association_showAll', []);
    }
}