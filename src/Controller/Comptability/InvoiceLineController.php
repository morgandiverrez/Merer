<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Invoice;
use App\Entity\Comptability\InvoiceLine;
use App\Form\Comptability\InvoiceLineType;
use App\Form\Comptability\TransactionLineType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/invoiceLine', name: 'invoiceLine_')]
class InvoiceLineController extends AbstractController
{
    #[Route('/new/{invoiceID}', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, $invoiceID, Request $request): Response
    {
        $invoiceLine = new InvoiceLine();
        $form = $this->createForm(InvoiceLineType::class, $invoiceLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoice = $entityManager->getRepository(Invoice::class)->findById($invoiceID);
            $invoiceLine->setInvoice($invoice );
            $entityManager->persist($invoiceLine);
            $entityManager->flush();

            return $this->redirectToRoute('invoice_show', ['invoiceID' => $invoiceID]);
        }
        return $this->render('Comptability/invoiceLine/edit.html.twig', [
            'invoiceLine' => $invoiceLine,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/edit/{invoiceLineId}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager,$invoiceLineId, Request $request): Response
    {
        $invoiceLine = $entityManager->getRepository(InvoiceLine::class)->findById($invoiceLineId);
        $form = $this->createForm(InvoiceLineType::class, $invoiceLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoice = $entityManager->getRepository(Invoice::class)->findById($invoiceLine->getInvoice()->getId());


            $invoiceLine->setInvoice($invoice);
            $entityManager->persist($invoiceLine);
            $entityManager->flush();
            return $this->redirectToRoute('invoice_show', ['invoiceID' => $invoiceLine->getInvoice()->getId()]);
        }

        return $this->render('Comptability/invoiceLine/edit.html.twig', [
            'invoiceLine' => $invoiceLine,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{invoiceLineID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $invoiceLineID): Response
    {

        $invoiceLine = $entityManager->getRepository(InvoiceLine::class)->findById($invoiceLineID);
        $entityManager->remove($invoiceLine);
        $entityManager->flush();


        return $this->redirectToRoute('invoice_show', ['invoiceID' => $invoiceLine->getInvoice()->getId()]);
    }
}
