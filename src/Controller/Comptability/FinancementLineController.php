<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Event;
use App\Entity\Comptability\Financement;
use App\Entity\Comptability\FinancementLine;
use App\Entity\Comptability\TransactionLine;
use App\Form\Comptability\FinancementLineType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/financementLine', name: 'financementLine_')]
class FinancementLineController extends AbstractController
{


    #[Route('/new/{financementID}', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, $financementID, Request $request): Response
    {
        $financementLine = new FinancementLine();
        $form = $this->createForm(FinancementLineType::class, $financementLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $financement = $entityManager->getRepository(Financement::class)->findFinancementById($financementID);

            $financementLine->setFinancement($financement);
            $entityManager->persist($financementLine);
            $entityManager->flush();

            return $this->redirectToRoute('financement_show', ['financementID' => $financementID]);
        }



        return $this->render('Comptability/financement_line/edit.html.twig', [
            'financementLine' => $financementLine,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/edit/{financementID}/{financementLineID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, $financementID, $financementLineID, Request $request): Response
    {
        $financementLine = $entityManager->getRepository(FinancementLine::class)->findById($financementLineID);
        $form = $this->createForm(FinancementLineType::class, $financementLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $financement = $entityManager->getRepository(Financement::class)->findFinancementById($financementID);
            $financementLine->setFinancement($financement);
            $entityManager->persist($financementLine);
            $entityManager->flush();
            return $this->redirectToRoute('financement_show', ['financementID' => $financementID]);
        }

        return $this->render('Comptability/financement_line/edit.html.twig', [
            'financement' => $financementLine,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{financementLineID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $financementLineID): Response
    {
        $financementLine = $entityManager->getRepository(FinancementLine::class)->findFinancementLineById($financementLineID);
        $total= $entityManager->getRepository(TransactionLine::class)->totalByFinancementLine($financementLine)['total'];

        $totalsTransactions = [];
        foreach ($financementLine->getTransactions() as $transaction) {
            $totalsTransactions[$transaction->getId()] = $entityManager->getRepository(TransactionLine::class)->totalByTransaction($transaction->getId())['total'];
        }
        $totalsEvents = [];
        foreach ($financementLine->getEvents() as $event) {
            $totalsEvents[$event->getId()] = $entityManager->getRepository(TransactionLine::class)->totalByEvent($event)['total'];
        }

        return $this->render('Comptability/financement_line/show.html.twig', [
            'financementLine' => $financementLine,
            'total' => $total,
            'totalsTransactions' => $totalsTransactions,
            'totalsEvents' => $totalsEvents,

        ]);
    }

    #[Route('/delete/{financementLineID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $financementLineID): Response
    {
        $financementLine = $entityManager->getRepository(FinancementLine::class)->findFinancementLineById($financementLineID);
        $entityManager->remove($financementLine);
        $entityManager->flush();
        return $this->redirectToRoute('financement_show', ['financementID' => $financementLine->getFinancement()->getId()]);
    }
}
