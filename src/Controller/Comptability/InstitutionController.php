<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Institution;
use App\Form\Comptability\InstitutionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/institution', name: 'institution_')]
class InstitutionController extends AbstractController
{
    #[Route('/edit/{institutionID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $institutionID): Response
    {
        $institution = $entityManager->getRepository(Institution::class)->findById($institutionID)[0];
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($institution);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show', []);
        }

        return $this->render('Comptability/institution/new.html.twig', [
            'institution' => $institution,
            'form' => $form->createView(),

        ]);
    }



    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $institution = new Institution();
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($institution);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show');
        }

        return $this->render('Comptability/institution/new.html.twig', [
            'institution' => $institution,
            'controler_title' => "new",
            'form' => $form->createView(),
        ]);
    }

}
