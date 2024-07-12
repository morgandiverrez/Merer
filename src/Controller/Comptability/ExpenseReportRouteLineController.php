<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Customer;
use App\Entity\Comptability\ExpenseReport;
use App\Entity\Comptability\ExpenseReportLine;
use App\Entity\Comptability\ExpenseReportRouteLine;
use App\Form\Comptability\ExpenseReportRouteLineType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/expenseReportRouteLine', name: 'expenseReportRouteLine_')]
class ExpenseReportRouteLineController extends AbstractController
{

    #[Route('/new/{expenseReportID}', name: 'new')]
    #[IsGranted('ROLE_USER')]
    public function new(EntityManagerInterface $entityManager, $expenseReportID, Request $request): Response
    {
        $expenseReportRouteLine = new ExpenseReportRouteLine();
        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportID);

         $user = $this->getUser();
        $customer = $user->getCustomer();

        if (($customer == $expenseReport->getSupplier()->getCustomer() or $this->isGranted("ROLE_TRESO")) and !$expenseReport->isComfirm()) {
            $form = $this->createForm(ExpenseReportRouteLineType::class, $expenseReportRouteLine);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
               
            
                if ($expenseReportRouteLine->getDistance() <= 50) {
                    $expenseReportRouteLine->setAmount($expenseReportRouteLine->getDistance() * 0.15);
                } else {
                    $expenseReportRouteLine->setAmount($expenseReportRouteLine->getDistance() * 0.20);
                }

                
                $expenseReportRouteLine->setExpenseReport($expenseReport);
                $entityManager->persist($expenseReportRouteLine);
                $entityManager->flush();

                if($customer->getBankDetails()[0]){
                    if($customer->getBankDetails()[0]->getIBAN() and $customer->getBankDetails()[0]->getBIC()){
                        return $this->redirectToRoute('expenseReport_show', ['expenseReportID' => $expenseReport->getId()]);
                    }
                }
                return $this->redirectToRoute('bankDetail_new', []);
            }
            return $this->render('Comptability/expense_report_route_line/edit.html.twig', [
                'expenseReportRouteLine' => $expenseReportRouteLine,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('account');
        }
    }


    #[Route('/edit/{expenseReportID}/{expenseReportRouteLineID}', name: 'edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(EntityManagerInterface $entityManager, $expenseReportID, $expenseReportRouteLineID, Request $request): Response
    {

        $expenseReportRouteLine = $entityManager->getRepository(ExpenseReportRouteLine::class)->findById($expenseReportRouteLineID)[0];
        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportID);

         $user = $this->getUser();
        $customer = $user->getCustomer();

        if (($customer == $expenseReport->getSupplier()->getCustomer() or $this->isGranted("ROLE_TRESO")) and !$expenseReport->isComfirm()) {
            $form = $this->createForm(ExpenseReportRouteLineType::class, $expenseReportRouteLine);
             $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportRouteLine->getExpenseReport()->getId());

                if ($expenseReportRouteLine->getDistance() <= 50) {
                    $expenseReportRouteLine->setAmount($expenseReportRouteLine->getDistance() * 0.15);
                } else {
                    $expenseReportRouteLine->setAmount($expenseReportRouteLine->getDistance() * 0.20);
                }

                $expenseReportRouteLine->setExpenseReport($expenseReport);
                $entityManager->persist($expenseReportRouteLine);
                $entityManager->flush();

                if($customer->getBankDetails()[0]){
                    if($customer->getBankDetails()[0]->getIBAN() and $customer->getBankDetails()[0]->getBIC()){
                        return $this->redirectToRoute('expenseReport_show', ['expenseReportID' => $expenseReport->getId()]);
                    }
                }
                return $this->redirectToRoute('bankDetail_new', []);
            }



            return $this->render('Comptability/expense_report_route_line/edit.html.twig', [
                'expenseReportRouteLine' => $expenseReportRouteLine,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('account');
        }
    }

    #[Route('/delete/{expenseReportRouteLineID}', name: 'delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $entityManager, $expenseReportRouteLineID): Response
    {

        $expenseReportRouteLine = $entityManager->getRepository(ExpenseReportRouteLine::class)->findById($expenseReportRouteLineID)[0];
        $expenseReport = $expenseReportRouteLine->getExpenseReport();

         $user = $this->getUser();
        $customer = $user->getCustomer();

        if (($customer == $expenseReport->getSupplier()->getCustomer() or $this->isGranted("ROLE_TRESO")) and !$expenseReport->isComfirm()) {
            $entityManager->remove($expenseReportRouteLine);
            $entityManager->flush();
            if($customer->getBankDetails()[0]){
                    if($customer->getBankDetails()[0]->getIBAN() and $customer->getBankDetails()[0]->getBIC()){
                        return $this->redirectToRoute('expenseReport_show', ['expenseReportID' => $expenseReport->getId()]);
                    }
                }
                return $this->redirectToRoute('bankDetail_new', []);
        } else {
            return $this->redirectToRoute('account');
        }

        
    }
}