<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profil', name: 'profil_')]


class ProfilController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_ADMIN')]
    public function showAll(EntityManagerInterface $entityManager): Response
    {
        $profils = $entityManager->getRepository(Profil::class)->findAll();

        return $this->render('profil/showAll.html.twig', [
            'profils' => $profils,
        ]);
    }

    #[Route('/show/{profilID}', name: 'show')]
    public function show(EntityManagerInterface $entityManager, $profilID): Response
    {
        // find renvoi tjr un array (tableau), donc faut mettre [0] pour enlever l'array, si on veut plus d'une valeur s'il y en a, on met pas ou [nombre]
        $profil = $entityManager->getRepository(Profil::class)->findById($profilID)[0];

        return $this->render('profil/show.html.twig', [
            'profil' => $profil,
        ]);
    }

    #[Route('/edit/{profilID}', name: 'edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $profilID): Response
    {
        $profil = $entityManager->getRepository(Profil::class)->findById($profilID)[0];
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $profil->setLastName(mb_strtoupper($profil->getLastName()));
            $profil->setName(mb_convert_case($profil->getName(), MB_CASE_TITLE, "UTF-8"));
            //$equipeElus = $entityManager->getRepository(EquipeElu::class)->findById()[0];
            //$profil->addEquipeElu();
            //$profil->addAssociation();

            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('profil_show',['profilID' => $profil->getID()]);
        }

        return $this->render('profil/edit.html.twig', [
            'profil' => $profil,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $profil = new Profil();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $profil->setLastName(mb_strtoupper($profil->getLastName()));
            $profil->setName(mb_convert_case($profil->getName(), MB_CASE_TITLE, "UTF-8"));
            //$equipeElus = $entityManager->getRepository(EquipeElu::class)->findById()[0];
            //$profil->addEquipeElu();
            //$profil->addAssociation();

            $entityManager->persist($profil);
            $entityManager->flush();

           
            return $this->redirectToRoute('profil_show', ['profilID' => $profil->getID()]);
        }

        return $this->render('profil/edit.html.twig', [
            'profil' => $profil,
            'form' => $form->createView(),
        ]);
    }
    
}
