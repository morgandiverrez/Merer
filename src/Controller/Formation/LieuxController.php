<?php

namespace App\Controller\Formation;

use App\Entity\Formation\Lieux;
use App\Form\Formation\LieuxType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/lieux', name: 'lieux_')]

class LieuxController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $lieux = $entityManager->getRepository(Lieux::class)->findAll();
       
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $lieux = array_intersect($lieux, $entityManager->getRepository(Lieux::class)->findAllByName($posts['name']));
            }
            if ($posts['ville']) {
                $lieux = array_intersect($lieux, $entityManager->getRepository(Lieux::class)->findAllByVille($posts['ville']));
            }
        }
        
        return $this->render('Formation_/lieux/showAll.html.twig', [
            'lieux' => $lieux,
          
        ]);
    }

    #[Route('/show/{lieuID}', name: 'show')]
    #[IsGranted('ROLE_USER')]
    public function show(EntityManagerInterface $entityManager, $lieuID): Response
    {
       
        $lieu = $entityManager->getRepository(Lieux::class)->findById($lieuID)[0];
        
        return $this->render('Formation_/lieux/show.html.twig', [
            'lieu' => $lieu,
            
        ]);
    }

    #[Route('/edit/{lieuID}', name: 'edit')]
    #[IsGranted('ROLE_BF')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $lieuID): Response
    {
        $lieu = $entityManager->getRepository(Lieux::class)->findById($lieuID)[0];
        $form = $this->createForm(LieuxType::class, $lieu);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('lieux_show', ['lieuID' => $lieuID]);
        }

        return $this->render('Formation_/lieux/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_BF')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $lieu = new Lieux();
        $form = $this->createForm(LieuxType::class, $lieu);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('lieux_showAll', []);
        }

        return $this->render('Formation_/lieux/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
            'user' => $user,

        ]);
    }

    #[Route('/delete/{lieuID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $lieuID): Response
    {

        $lieu = $entityManager->getRepository(Lieux::class)->findById($lieuID)[0];
        $entityManager->remove($lieu);
        $entityManager->flush();
        

        return $this->redirectToRoute('lieux_showAll', []);
    }

}
