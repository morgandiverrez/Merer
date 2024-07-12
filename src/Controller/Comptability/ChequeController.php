<?php

namespace App\Controller\Comptability;


use App\Entity\Comptability\Cheque;
use App\Form\Comptability\ChequeType;
use App\Entity\Comptability\ChequeBox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cheque', name: 'cheque_')]
class ChequeController extends AbstractController
{
  

   

    #[Route('/new/{chequeBoxID}', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, $chequeBoxID , Request $request): Response
    {
        $cheque = new Cheque();
        $form = $this->createForm(ChequeType::class, $cheque);
        $form->handleRequest($request);
        $chequeBox = $entityManager->getRepository(ChequeBox::class)->findById($chequeBoxID);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $cheque->setChequeBox($chequeBox);
            $entityManager->persist($cheque);
            $entityManager->flush();
            return $this->redirectToRoute('chequeBox_show', ['chequeBoxID' => $chequeBoxID ]);
        }

        return $this->render('Comptability/cheque/new.html.twig', [
            'cheque' => $cheque,
            'form' => $form->createView(),
        ]);
    }

 
    #[Route('/edit/{chequeID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $chequeID): Response
    {
        $cheque = $entityManager->getRepository(Cheque::class)->findById($chequeID)[0];
        $form = $this->createForm(ChequeType::class, $cheque);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($cheque);
            $entityManager->flush();
            return $this->redirectToRoute('chequeBox_show', ['chequeBoxID' => $cheque->getChequeBox()->getId()]);
        }

        return $this->render('Comptability/cheque/edit.html.twig', [
            'cheque' => $cheque,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{chequeID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $chequeID): Response
    {

        $cheque = $entityManager->getRepository(cheque::class)->findById($chequeID)[0];
        $entityManager->remove($cheque);
        $entityManager->flush();

        return $this->redirectToRoute('chequeBox_show', ['chequeBoxID' => $cheque->getChequeBox()->getId()]);
    }

}
