<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Exercice;
use App\Entity\Comptability\Transaction;
use App\Form\Comptability\TransactionType;
use App\Entity\Comptability\TransactionLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/transaction', name: 'transaction_')]
class TransactionController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface$entityManager, Request $request): Response
    {
        $transactions = $entityManager->getRepository(Transaction::class)->findAll();

        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['commentaire']) {
                $transactions = array_intersect($transactions, $entityManager->getRepository(Transaction::class)->findAllByCommentaire($posts['commentaire']));
            }
            if ($posts['code']) {
                $transactions = array_intersect($transactions, $entityManager->getRepository(Transaction::class)->findAllByCode($posts['code']));
            }
            if ($posts['cloture'] == '1') {
                $transactions = array_intersect($transactions, $entityManager->getRepository(Transaction::class)->findAllComfirm());
            }
            if ($posts['cloture'] == '0') {
                $transactions = array_intersect($transactions, $entityManager->getRepository(Transaction::class)->findAllNotComfirm());
            }
        }
        return $this->render('Comptability/transaction/showAll.html.twig', [
            'transactions' => $transactions,
          
        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));

        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($entityManager->getRepository(Transaction::class)->findMaxDayTransaction(date("Ymd") * 100)[0])){
                $nbtransaction = $entityManager->getRepository(Transaction::class)->findMaxDayTransaction(date("Ymd") * 100)[0]['code'];
                $transaction->setCode($nbtransaction + 1);
            }else{
                $nbtransaction = 0;    
                 $transaction->setCode(date("Ymd") * 100 + $nbtransaction + 1);
            }
            foreach($transaction->getTransactionLines() as $transactionLine){
                
                $entityManager->persist($transactionLine);
                
            }
            $entityManager->persist($transaction);
            $entityManager->flush();
            
            $transaction = $entityManager->getRepository(Transaction::class)->findTransactionByCode($transaction->getCode());

            $i=0;
            
            foreach($form->get('transactionLines') as $transactionLine){
                $logoUpload = $transactionLine->get('urlProof')->getData();
                if ($logoUpload) {
                    $urlProof = 'transactionLineProof_' . $transaction->getTransactionLines()[$i]->getId() . '.' . $logoUpload[0]->guessExtension();


                    $transaction->getTransactionLines()[$i]->setUrlProof("build/transaction/".$transaction->getCode()."/" . $urlProof);
                    try {
                        $logoUpload[0]->move(
                            "build/transactionComptability//".$transaction->getCode()."/",
                            $urlProof
                        );
                    } catch (FileException $e) {
                    }
                }
                $entityManager->persist($transaction->getTransactionLines()[$i]);
                $i++;
            }
            
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('transaction_show', ['transactionId' => $transaction->getId()]);
        }

        return $this->render('Comptability/transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{transactionId}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, $transactionId, Request $request): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));

        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $transaction = $entityManager->getRepository(Transaction::class)->findTransactionById($transactionId);
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);
       

        if ($form->isSubmitted() && $form->isValid()) {
            
            foreach ($transaction->getTransactionLines() as $transactionLine) {
                $transaction->addTransactionLine($transactionLine);
                $entityManager->persist($transactionLine);
            }
            $entityManager->persist($transaction);
            $entityManager->flush();

            $transaction = $entityManager->getRepository(Transaction::class)->findTransactionByCode($transaction->getCode());

            $i = 0;

            foreach ($form->get('transactionLines') as $transactionLine) {
                $logoUpload = $transactionLine->get('urlProof')->getData();
                if ($logoUpload) {
                    $urlProof = 'transactionLineProof' . $transaction->getTransactionLines()[$i]->getId() . '.' . $logoUpload[0]->guessExtension();
                    $transaction->getTransactionLines()[$i]->setUrlProof('public/build/transactionLine/proof/' . $urlProof);
                    try {
                        $logoUpload[0]->move(
                            'public/build/transactionLine/proof',
                            $urlProof
                        );
                    } catch (FileException $e) {
                    }
                }
                if ($logoUpload) {
                
                    //on créer le nouveau chemin du nouveau doc
                    $generalPath =  "build/transaction/".$transaction->getCode()."/";
                    $newDocument = 'transactionLineProof_' . $transaction->getTransactionLines()[$i]->getId() . '.' . $logoUpload[0]->guessExtension();
                    $newPath =$generalPath.$newDocument;

                    //on recupere l'extension du doc à déplacer 
                    $oldPathOldDocument = $transaction->getTransactionLines()[$i]->getUrlProof();
                    $element = explode(".", $oldPathOldDocument);
                    $oldExtension = end($element);

                    //verif si le dossier old existe
                    if (!is_dir($generalPath.'old')) {
                        mkdir($generalPath.'old/');
                    }

                    if(glob($generalPath.'old/oldTransactionLineProof_'. $transaction->getTransactionLines()[$i]->getId()."_*")){
                        $listOldProof = glob($generalPath.'old/oldTransactionLineProof_'. $transaction->getTransactionLines()[$i]->getId()."_*");
                        $LastProof =end($listOldProof);
                        $listPartPathLastProof = explode("_", $LastProof);
                        $strNumberLastProof = end($listPartPathLastProof);
                        $intNumberLastProof = intval($strNumberLastProof);
                        $oldPath =$generalPath.'old/oldTransactionLineProof_'. $transaction->getTransactionLines()[$i]->getId()."_".$intNumberLastProof+1 . '.'.$oldExtension;
                        rename($newPath, $oldPath);
                    }else{
                        $oldPath =$generalPath.'old/oldTransactionLineProof_'. $transaction->getTransactionLines()[$i]->getId()."_1".'.'.$oldExtension;
                        rename($newPath, $oldPath);
                    }
                    
                    
                        //on stock le nouveau chemin 
                    $transaction->getTransactionLines()[$i]->setUrlProof($newPath);
                    try {
                        $logoUpload[0]->move(
                            $generalPath,
                            $newDocument
                        );
                    } catch (FileException $e) {
                    }
                }
                $entityManager->persist($transaction->getTransactionLines()[$i]);
                $i++;
            }
            $entityManager->persist($transaction);
            $entityManager->flush();
            return $this->redirectToRoute('transaction_show', ['transactionId' => $transaction->getId()]);
        }

        return $this->render('Comptability/transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{transactionId}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $transactionId): Response
    {
        $transaction = $entityManager->getRepository(Transaction::class)->findTransactionById($transactionId);
        return $this->render('Comptability/transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/download/{transactionLineId}', name: 'download')]
    #[IsGranted('ROLE_TRESO')]
    public function download(EntityManagerInterface $entityManager,  $transactionLineId)
    {
        $transactionLine = $entityManager->getRepository(TransactionLine::class)->findById($transactionLineId);

        $finaleFile = $transactionLine->getUrlProof();

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($finaleFile) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($finaleFile));
        readfile($finaleFile);

        return $this->redirectToRoute('transaction_show', ['transactionId' => $transactionLine->getTransaction()->getId()]);
    }

    #[Route('/closure/{transactionId}', name: 'cloture')]
    #[IsGranted('ROLE_TRESO')]
    public function closure(EntityManagerInterface $entityManager, $transactionId): Response
    {
        $transaction = $entityManager->getRepository(Transaction::class)->findTransactionById($transactionId);
        $transaction->setClosure(true);

        if ($transaction->getInvoice()) $transaction->getInvoice()->setAcquitted(true);
        $entityManager->persist($transaction);
        $entityManager->persist($transaction->getInvoice());
        $entityManager->flush();
        return $this->redirectToRoute('transaction_showAll');
    }

    #[Route('/unclosure/{transactionId}', name: 'uncloture')]
    #[IsGranted('ROLE_TRESO')]
    public function unclosure(EntityManagerInterface $entityManager, $transactionId): Response
    {
        $transaction = $entityManager->getRepository(Transaction::class)->findTransactionById($transactionId);
        if ($transaction->getInvoice()) $transaction->getInvoice()->setAcquitted(false);
        $transaction->setClosure(false);
        $entityManager->persist($transaction->getInvoice());
        $entityManager->persist($transaction);
        $entityManager->flush();
        return $this->redirectToRoute('transaction_showAll');
    }

    #[Route('/delete/{transactionID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $transactionID): Response
    {

        $transaction = $entityManager->getRepository(Transaction::class)->findTransactionById($transactionID);
        $entityManager->remove($transaction);
        $entityManager->flush();

        return $this->redirectToRoute('transaction_showAll');
    }

}
