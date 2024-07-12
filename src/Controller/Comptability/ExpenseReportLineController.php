<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Customer;
use App\Entity\Comptability\Transaction;
use App\Entity\Comptability\ExpenseReport;
use App\Entity\Comptability\TransactionLine;
use App\Entity\Comptability\ExpenseReportLine;
use App\Form\Comptability\TransactionLineType;
use App\Form\Comptability\ExpenseReportLineType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/expenseReportLine', name: 'expenseReportLine_')]
class ExpenseReportLineController extends AbstractController
{

    #[Route('/new/{expenseReportID}', name: 'new')]
    #[IsGranted('ROLE_USER')]
    public function new(EntityManagerInterface $entityManager, $expenseReportID, Request $request): Response
    {
        $expenseReportLine = new ExpenseReportLine();
        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportID);

         $user = $this->getUser();
        $customer = $user->getCustomer();

        if (($customer == $expenseReport->getSupplier()->getCustomer() or $this->isGranted("ROLE_TRESO")) and !$expenseReport->isComfirm()) {
            $form = $this->createForm(ExpenseReportLineType::class, $expenseReportLine);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                $expenseReportLine->setExpenseReport($expenseReport);
                $entityManager->persist($expenseReportLine);
                $entityManager->flush();
                $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportID);
                $logoUpload = $form->get('document')->getData();
                 if ($logoUpload) {
                    
                    //on créer le nouveau chemin du nouveau doc
                    $generalPath =  "build/expenseReport/".$expenseReport->getCode()."/";
                    $newDocument = 'expenseReportLineProof_' .  $expenseReportLine->getId() . '.' . $logoUpload[0]->guessExtension();
                    $newPath =$generalPath.$newDocument;

                     //on stock le nouveau chemin 
                    $expenseReportLine->setDocument($newPath);
                    try {
                        $logoUpload[0]->move(
                            $generalPath,
                            $newDocument
                        );
                    } catch (FileException $e) {
                    }
                }
                $expenseReportLine->setExpenseReport($expenseReport);
                $entityManager->persist($expenseReportLine);
                $entityManager->flush();

                if($customer->getBankDetails()[0]){
                    if($customer->getBankDetails()[0]->getIBAN() and $customer->getBankDetails()[0]->getBIC()){
                        return $this->redirectToRoute('expenseReport_show', ['expenseReportID' => $expenseReport->getId()]);
                    }
                }else{
                     return $this->redirectToRoute('bankDetail_new', []);
                }
            }



            return $this->render('Comptability/expense_report_line/edit.html.twig', [
                'expenseReportLine' => $expenseReportLine,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('account');
        }
    }


    #[Route('/edit/{expenseReportID}/{expenseReportLineID}', name: 'edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(EntityManagerInterface $entityManager, $expenseReportID, $expenseReportLineID, Request $request): Response
    {
        $expenseReportLine = $entityManager->getRepository(ExpenseReportLine::class)->findById($expenseReportLineID)[0];

        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportID);

         $user = $this->getUser();
        $customer = $user->getCustomer();

        if (($customer == $expenseReport->getSupplier()->getCustomer() or $this->isGranted("ROLE_TRESO")) and !$expenseReport->isComfirm()) {
            $form = $this->createForm(ExpenseReportLineType::class, $expenseReportLine);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportID);
                $expenseReportLine->setExpenseReport($expenseReport);
                $entityManager->persist($expenseReportLine);
                $entityManager->flush();

                $logoUpload = $form->get('document')->getData();
                if ($logoUpload) {
                    
                    //on créer le nouveau chemin du nouveau doc
                    $generalPath =  "build/expenseReport/".$expenseReport->getCode()."/";
                    $newDocument = 'expenseReportLineProof_' .  $expenseReportLine->getId() . '.' . $logoUpload[0]->guessExtension();
                    $newPath =$generalPath.$newDocument;

                    //on recupere l'extension du doc à déplacer 
                    $oldPathOldDocument = $expenseReportLine->getDocument();
                    $element = explode(".", $oldPathOldDocument);
                    $oldExtension = end($element);

                    //verif si le dossier old existe
                    if (!is_dir($generalPath.'old')) {
                        mkdir($generalPath.'old/');
                    }

                    if(glob($generalPath.'old/oldExpenseReportLineProof_'. $expenseReportLine->getId()."_*")){
                        $listOldProof = glob($generalPath.'old/oldExpenseReportLineProof_'. $expenseReportLine->getId()."_*");
                        $LastProof =end($listOldProof);
                        $listPartPathLastProof = explode("_", $LastProof);
                        $strNumberLastProof = end($listPartPathLastProof);
                        $intNumberLastProof = intval($strNumberLastProof);
                        $oldPath =$generalPath.'old/oldExpenseReportLineProof_'. $expenseReportLine->getId()."_".$intNumberLastProof+1 . '.'.$oldExtension;
                        rename($newPath, $oldPath);
                    }else{
                        $oldPath =$generalPath.'old/oldExpenseReportLineProof_'. $expenseReportLine->getId()."_1".'.'.$oldExtension;
                        rename($newPath, $oldPath);
                    }
                   
                    
                     //on stock le nouveau chemin 
                    $expenseReportLine->setDocument($newPath);
                    try {
                        $logoUpload[0]->move(
                            $generalPath,
                            $newDocument
                        );
                    } catch (FileException $e) {
                    }
                }
                $expenseReportLine->setExpenseReport($expenseReport);
                $entityManager->persist($expenseReportLine);
                $entityManager->flush();

                if($customer->getBankDetails()[0]){
                    if($customer->getBankDetails()[0]->getIBAN() and $customer->getBankDetails()[0]->getBIC()){
                        return $this->redirectToRoute('expenseReport_show', ['expenseReportID' => $expenseReport->getId()]);
                    }
                }
                return $this->redirectToRoute('bankDetail_new', []);
            }



            return $this->render('Comptability/expense_report_line/edit.html.twig', [
                'expenseReportLine' => $expenseReportLine,
                'form' => $form->createView(),
            ]);
         } else {
            return $this->redirectToRoute('account');
        }
    }

    #[Route('/delete/{expenseReportLineID}', name: 'delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $entityManager, $expenseReportLineID): Response
    {
        
        $expenseReportLine = $entityManager->getRepository(ExpenseReportLine::class)->findById($expenseReportLineID)[0];
        $expenseReport = $expenseReportLine->getExpenseReport();

         $user = $this->getUser();
        $customer = $user->getCustomer();

        if (($customer == $expenseReport->getSupplier()->getCustomer() or $this->isGranted("ROLE_TRESO")) and !$expenseReport->isComfirm()) {
            $path = $expenseReportLine->getDocument();
            $elements = explode(".", $path);
            $extension = end($elements);
            $document = 'expenseReportLineProof_' .  $expenseReportLine->getId() . '.' . $extension;
            $patho =  "build/expenseReport/".$expenseReport->getCode()."/";
            if (glob ($path)) {
                $pathOld = $patho."old/".$document;
                if (!is_dir($patho.'old/')) {
                    mkdir($patho.'old/');
                }
                $i=0;
                while(is_dir($pathOld)){
                    $pathOld = $patho."old/expenseReportLineProof_" .  $expenseReportLine->getId()."_".$i . '.' . $extension;
                    $i++;
                }
                rename($path, $pathOld);
            }

            
            $entityManager->remove($expenseReportLine);
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

    #[Route('/download/{expenseReportLineID}', name: 'download')]
    #[IsGranted('ROLE_USER')]
    public function download(EntityManagerInterface $entityManager,  $expenseReportLineID)
    {
        $expenseReportLine = $entityManager->getRepository(ExpenseReportLine::class)->findById($expenseReportLineID)[0];
        $expenseReport = $expenseReportLine->getExpenseReport();

         $user = $this->getUser();
        if ($user == $expenseReport->getSupplier()->getCustomer()->getUser() or $this->isGranted("ROLE_TRESO")) {
            $finaleFile = $expenseReportLine->getDocument();
            echo($finaleFile);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($finaleFile) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($finaleFile));
            readfile($finaleFile);
            return $this->redirectToRoute('expenseReport_show', ['expenseReportID' => $expenseReport->getId()]);
            
         } else {
            return $this->redirectToRoute('account');
        }
        
    }
}
