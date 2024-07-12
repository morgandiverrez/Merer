<?php

namespace App\Controller\Comptability;

use DateTime;
use DateInterval;
use App\Entity\Comptability\Invoice;
use App\Entity\Comptability\PaymentDeadline;
use App\Form\Comptability\PaymentDeadlineType;
use App\Controller\Comptability\InvoiceController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/paymentDeadline', name: 'paymentDeadline_')]
class PaymentDeadlineController extends AbstractController
{

    #[Route('/new/{invoiceId}', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request, $invoiceId): Response
    {
        $paymentDeadline = new PaymentDeadline();
        $form = $this->createForm(PaymentDeadlineType::class, $paymentDeadline);
        $form->handleRequest($request);
        $invoice = $entityManager->getRepository(Invoice::class)->findById($invoiceId);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paymentDeadline);
            

            $invoice->addPaymentdeadline($paymentDeadline);
            $entityManager->persist($invoice);
            $entityManager->flush();
            return $this->redirectToRoute('invoice_show', ['invoiceID'=> $invoice->getId()]);
        }

        return $this->render('Comptability/paymentDeadline/new.html.twig', [
            'paymentDeadline' => $paymentDeadline,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/edit/{paymentDeadlineID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $paymentDeadlineID): Response
    {
        $paymentDeadline = $entityManager->getRepository(PaymentDeadline::class)->findById($paymentDeadlineID)[0];
        $form = $this->createForm(PaymentDeadlineType::class, $paymentDeadline);
        $form->handleRequest($request);
        $invoice = $paymentDeadline->getInvoice();


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paymentDeadline);
            $invoice->addPaymentdeadline($paymentDeadline);
            $entityManager->persist($invoice);
            $entityManager->flush();
            return $this->redirectToRoute('invoice_show', ['invoiceID' => $invoice->getId()]);
        }

        return $this->render('Comptability/paymentDeadline/new.html.twig', [
            'paymentDeadline' => $paymentDeadline,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create/{invoiceId}/{nb}', name: 'create')]
    #[IsGranted('ROLE_TRESO')]
    public function create(EntityManagerInterface $entityManager, $invoiceId, $nb): Response
    {

        $invoice = $entityManager->getRepository(Invoice::class)->findById($invoiceId);
        $totale = (new InvoiceController)->invoiceTotale($invoice);

        for ($i = 0; $i < $nb; $i++) {
            $paymentDeadline = new PaymentDeadline();
            $paymentDeadline->setExpectedAmount(intdiv($totale, $nb));
            if ($i == 0) {
                $paymentDeadline->setExpectedAmount($paymentDeadline->getExpectedAmount() + $totale % $nb);
            }
            $date = new DateTime();
            $date->add(new DateInterval("P" . ($i + 1) . "M"));
            $paymentDeadline->setExpectedPaymentDate($date);
            $paymentDeadline->setExpectedMeans("Chéque");

            $entityManager->persist($paymentDeadline);

            $invoice->addPaymentdeadline($paymentDeadline);

            $entityManager->persist($invoice);
       
        }
        $entityManager->flush();
        return $this->redirectToRoute('customeaccount_invoiceTable');
    }

    #[Route('/delete/{paymentDeadlineID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $paymentDeadlineID): Response
    {

        $paymentDeadline = $entityManager->getRepository(PaymentDeadline::class)->findById($paymentDeadlineID)[0];
        $entityManager->remove($paymentDeadline);
        $entityManager->flush();


        return $this->redirectToRoute('invoice_show', ['invoiceID' => $paymentDeadline->getInvoice()->getId()]);
    }
}
