<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Financement;
use App\Form\Comptability\FinancementType;
use App\Entity\Comptability\TransactionLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/financement', name: 'financement_')]
class FinancementController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface$entityManager, Request $request ): Response
    {
        $financements = $entityManager->getRepository(Financement::class)->findAllInOrder();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $financements = array_intersect($financements, $entityManager->getRepository(Financement::class)->findAllByName($posts['name']));
            }
            if ($posts['financeur']) {
                $financements = array_intersect($financements, $entityManager->getRepository(Financement::class)->findAllByFinanceur($posts['financeur']));
            } 
        }
       
        return $this->render('Comptability/financement/showAll.html.twig', [
            'financements' => $financements,

        ]);
    }

    #[Route('/show/{financementID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $financementID): Response
    {
        $financement = $entityManager->getRepository(Financement::class)->findFinancementById($financementID);
        $total = [];
        foreach($financement->getFinancementLines() as $financementLine){
            $total[$financementLine->getId()] =$entityManager->getRepository(TransactionLine::class)->totalByFinancementLine($financementLine)['total'];
        }
        return $this->render('Comptability/financement/show.html.twig', [
            'financement' => $financement,
            'total' => $total,

        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $financement = new Financement();
        $form = $this->createForm(FinancementType::class, $financement);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($financement->getFinancementLines() as $financementLine) {
                $entityManager->persist($financementLine);
            }
            $entityManager->persist($financement);
            $entityManager->flush();
            return $this->redirectToRoute('financement_show', ['financementID' => $financement->getId()]);
        }

        return $this->render('Comptability/financement/new.html.twig', [
            'financement' => $financement,
            'form' => $form->createView(),


        ]);
    }



    #[Route('/edit/{financementID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $financementID): Response
    {
        $financement = $entityManager->getRepository(Financement::class)->findFinancementById($financementID);
        $form = $this->createForm(FinancementType::class, $financement);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($financement->getFinancementLines() as $financementLine) {
                $entityManager->persist($financementLine);
            }
            $entityManager->persist($financement);
            $entityManager->flush();
            return $this->redirectToRoute('financement_show', ['financementID' => $financementID]);
        }

        return $this->render('Comptability/financement/edit.html.twig', [
            'financement' => $financement,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{financementID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $financementID): Response
    {

        $financement = $entityManager->getRepository(Financement::class)->findFinancementById($financementID);
        $entityManager->remove($financement);
        $entityManager->flush();

        return $this->redirectToRoute('financement_showAll');
    }

 
}
