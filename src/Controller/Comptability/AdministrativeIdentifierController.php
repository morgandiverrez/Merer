<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\AdministrativeIdentifier;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Comptability\AdministrativeIdentifierType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/administrativeIdentifier', name: 'administrativeIdentifier_')]
class AdministrativeIdentifierController extends AbstractController
{
    #[Route('/edit/{administrativeIdentifierID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $administrativeIdentifierID): Response
    {
        $administrativeIdentifier = $entityManager->getRepository(AdministrativeIdentifier::class)->findById($administrativeIdentifierID)[0];
        $form = $this->createForm(AdministrativeIdentifierType::class, $administrativeIdentifier);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($administrativeIdentifier);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show', []);
        }

        return $this->render('Comptability/administrative_identifier/new.html.twig', [
            'administrativeIdentifier' => $administrativeIdentifier,
            'form' => $form->createView(),

        ]);
    }

 

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $administrativeIdentifier = new AdministrativeIdentifier();
        $form = $this->createForm(AdministrativeIdentifierType::class, $administrativeIdentifier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($administrativeIdentifier);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show');
        }

        return $this->render('Comptability/administrative_identifier/new.html.twig', [
            'administrativeIdentifier' => $administrativeIdentifier,
            'form' => $form->createView(),
        ]);
    }

   
}
