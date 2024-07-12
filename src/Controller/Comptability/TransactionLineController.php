<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Transaction;
use App\Entity\Comptability\TransactionLine;
use App\Form\Comptability\TransactionLineType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/transactionLine', name: 'transactionLine_')]
class TransactionLineController extends AbstractController
{

    #[Route('/new/{transactionId}', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, $transactionId, Request $request): Response
    {
        $transactionLine = new TransactionLine();
        $form = $this->createForm(TransactionLineType::class, $transactionLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction = $entityManager->getRepository(Transaction::class)->findTransactionById($transactionId);
             
             $transactionLine->setTransaction($transaction);
             $entityManager->persist($transactionLine);
             $entityManager->persist($transaction);
            $entityManager->flush();
            $transactionLine = $entityManager->getRepository(TransactionLine::class)->findByTransactionAndLabel($transactionId, $form->get('label')->getData());
            $logoUpload = $form->get('urlProof')->getData();
            if ($logoUpload) {
                $urlProof = 'transactionLineProof_' . $transactionLine->getId() . '.' . $logoUpload[0]->guessExtension();


                $transactionLine->setUrlProof("build/transaction/".$transaction->getCode()."/" . $urlProof);
                try {
                    $logoUpload[0]->move(
                        "build/transaction/".$transaction->getCode()."/",
                        $urlProof
                    );
                } catch (FileException $e) {
                }
            }
            $transactionLine->setTransaction($transaction);
            $entityManager->persist($transactionLine);
            $entityManager->flush();

                return $this->redirectToRoute('transaction_show', ['transactionId' => $transactionId]);
        }

         

        return $this->render('Comptability/transactionLine/edit.html.twig', [
            'transaction' => $transactionLine,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/edit/{transactionId}/{transactionLineId}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, $transactionId, $transactionLineId, Request $request): Response
    {
        $transactionLine = $entityManager->getRepository(TransactionLine::class)->findById($transactionLineId);
        $form = $this->createForm(TransactionLineType::class, $transactionLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction = $entityManager->getRepository(Transaction::class)->findTransactionById($transactionId);

            $logoUpload = $form->get('urlProof')->getData();

            if ($logoUpload) {
                
                //on créer le nouveau chemin du nouveau doc
                $generalPath =  "build/transaction/".$transaction->getCode()."/";
                $newDocument = 'transactionLineProof_' . $transactionLine->getId() . '.' . $logoUpload[0]->guessExtension();
                $newPath =$generalPath.$newDocument;

                //on recupere l'extension du doc à déplacer 
                $oldPathOldDocument = $transactionLine->getUrlProof();
                $element = explode(".", $oldPathOldDocument);
                $oldExtension = end($element);

                //verif si le dossier old existe
                if (!is_dir($generalPath.'old')) {
                    mkdir($generalPath.'old/');
                }

                if(glob($generalPath.'old/oldTransactionLineProof_'. $transactionLine->getId()."_*")){
                    $listOldProof = glob($generalPath.'old/oldTransactionLineProof_'. $transactionLine->getId()."_*");
                    $LastProof =end($listOldProof);
                    $listPartPathLastProof = explode("_", $LastProof);
                    $strNumberLastProof = end($listPartPathLastProof);
                    $intNumberLastProof = intval($strNumberLastProof);
                    $oldPath =$generalPath.'old/oldTransactionLineProof_'. $transactionLine->getId()."_".$intNumberLastProof+1 . '.'.$oldExtension;
                    rename($newPath, $oldPath);
                }else{
                    $oldPath =$generalPath.'old/oldTransactionLineProof_'. $transactionLine->getId()."_1".'.'.$oldExtension;
                    rename($newPath, $oldPath);
                }
                
                
                    //on stock le nouveau chemin 
                $transactionLine->setUrlProof($newPath);
                try {
                    $logoUpload[0]->move(
                        $generalPath,
                        $newDocument
                    );
                } catch (FileException $e) {
                }
            }
            $transactionLine->setTransaction($transaction);
            $entityManager->persist($transactionLine);
            $entityManager->flush();
            return $this->redirectToRoute('transaction_show', ['transactionId' => $transactionId]);
        }

        return $this->render('Comptability/transactionLine/edit.html.twig', [
            'transaction' => $transactionLine,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{transactionLineID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $transactionLineID): Response
    {

        $transactionLine = $entityManager->getRepository(TransactionLine::class)->findById($transactionLineID);
         $generalPath =  "build/transaction/".$transactionLine->getTransaction()->getCode()."/";
        //on recupere l'extension du doc à déplacer 
        $oldPathOldDocument = $transactionLine->getUrlProof();
        $element = explode(".", $oldPathOldDocument);
        $oldExtension = end($element);

        //verif si le dossier old existe
        if (!is_dir($generalPath.'old')) {
            mkdir($generalPath.'old/');
        }

        if(glob($generalPath.'old/oldTransactionLineProof_'. $transactionLine->getId()."_*")){
            $listOldProof = glob($generalPath.'old/oldTransactionLineProof_'. $transactionLine->getId()."_*");
            $LastProof =end($listOldProof);
            $listPartPathLastProof = explode("_", $LastProof);
            $strNumberLastProof = end($listPartPathLastProof);
            $intNumberLastProof = intval($strNumberLastProof);
            $oldPath =$generalPath.'old/oldTransactionLineProof_'. $transactionLine->getId()."_".$intNumberLastProof+1 . '.'.$oldExtension;
            rename($oldPathOldDocument, $oldPath);
        }else{
            $oldPath =$generalPath.'old/oldTransactionLineProof_'. $transactionLine->getId()."_1".'.'.$oldExtension;
            rename($oldPathOldDocument, $oldPath);
        }  

        $entityManager->remove($transactionLine);
        $entityManager->flush();


        return $this->redirectToRoute('transaction_show', ['transactionId'=>$transactionLine->getTransaction()->getId()]);
    }
}
