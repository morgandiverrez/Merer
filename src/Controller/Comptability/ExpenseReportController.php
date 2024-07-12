<?php

namespace App\Controller\Comptability;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Aws\Ses\SesClient;
use App\Entity\Comptability\Supplier;
use App\Entity\Comptability\Exercice;
use Endroid\QrCode\QrCode;
use App\Entity\Comptability\Transaction;
use App\Entity\Comptability\Federation;
use App\Entity\Comptability\Institution;
use App\Entity\Comptability\ExpenseReport;
use App\Form\Comptability\TransactionType;
use Endroid\QrCode\Logo\Logo;
use App\Entity\Comptability\TransactionLine;
use App\Form\Comptability\ExpenseReportType;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use App\Entity\Comptability\ExpenseReportLine;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use App\Controller\Comptability\CustomerController;
use Endroid\QrCode\Label\Font\NotoSans;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/expenseReport', name: 'expenseReport_')]
class ExpenseReportController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $expenseReports = $entityManager->getRepository(ExpenseReport::class)->findAll();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['demandeur']) {
                $expenseReports = array_intersect($expenseReports, $entityManager->getRepository(ExpenseReport::class)->findAllByDemandeur($posts['demandeur']));
            }
            if ($posts['comfirmer'] == '1' ) {
                $expenseReports = array_intersect($expenseReports, $entityManager->getRepository(ExpenseReport::class)->findAllComfirm());
            }
            if ($posts['comfirmer'] == '0') {
                $expenseReports = array_intersect($expenseReports, $entityManager->getRepository(ExpenseReport::class)->findAllNotComfirm());
            }
        }
        return $this->render('Comptability/expense_report/showAll.html.twig', [
            'expenseReports' => $expenseReports,

        ]);
    }

     #[Route('/download/modalitéfinanciereremboursement', name: 'modalitéfinanciereremboursement')]
    public function download()
    {
          

        $finaleFile = "build/document/Modalités-financières-de-remboursement-2023-2025.pdf";

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($finaleFile) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($finaleFile));
        readfile($finaleFile);

        return $this->redirectToRoute('expenseReport/');
        
    }

    #[Route('/delete/{expenseReportID}', name: 'delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $entityManager, $expenseReportID): Response
    {

        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findById($expenseReportID)[0];
        


        if (($this->getUser() == $expenseReport->getSupplier()->getCustomer()->getUser() or $this->isGranted("ROLE_TRESO")) and !$expenseReport->isComfirm()) {
           
            foreach($expenseReport->getExpenseReportLines() as $expenseReportLine){
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
            }
             $entityManager->remove($expenseReport);
            $entityManager->flush();
            if($this->isGranted("ROLE_TRESO")){
                return $this->redirectToRoute('expenseReport_showAll', []);
            }else{
                return $this->redirectToRoute('account');
            }
        } else {
            return $this->redirectToRoute('account');
        }
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_USER')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));
        $user = $this->getUser();
        
        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $expenseReport = new ExpenseReport();
        $form = $this->createForm(ExpenseReportType::class, $expenseReport);
        $form->handleRequest($request);

        $customer = $this->getUser()->getCustomer();
        if( ! $customer){
           $customer = (new CustomerController)->newCustomerForUser(  $entityManager, $user);
        }

        $supplier = $customer->getSupplier();

        if ($form->isSubmitted() && $form->isValid()) {
      
            $expenseReport->setDate( new DateTime());
            $expenseReport->setSupplier($supplier);
            if (isset($entityManager->getRepository(ExpenseReport::class)->findMaxDayExpenseReport(date("Ymd") * 100)[0])) {
                $nbtransaction = $entityManager->getRepository(ExpenseReport::class)->findMaxDayExpenseReport(date("Ymd") * 100)[0]['code'];
                $expenseReport->setCode($nbtransaction + 1);
            } else {
                $nbtransaction = 0;
                $expenseReport->setCode(date("Ymd") * 100 + $nbtransaction + 1);
            }
            foreach ($expenseReport->getExpenseReportLines() as $expenseReportLine) {
                $entityManager->persist($expenseReportLine);
            }
           
            foreach ($expenseReport->getExpenseReportRouteLines() as $expenseReportRouteLine) {
                if($expenseReportRouteLine->getDistance() <= 50){
                    $expenseReportRouteLine->setAmount($expenseReportRouteLine->getDistance()*0.15);
                }else{
                     $expenseReportRouteLine->setAmount($expenseReportRouteLine->getDistance()*0.20);
                }
                $entityManager->persist($expenseReportRouteLine);

            }

            $entityManager->persist($expenseReport);
            $entityManager->persist($supplier);
            $entityManager->flush();

            $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportByCode($expenseReport->getCode());

            $i = 0;
            
            foreach ($form->get('expenseReportLines') as $expenseReportLine) {
                $logoUpload = $expenseReportLine->get('document')->getData();
                if ($logoUpload) {
                    $document = 'expenseReportLineProof_' .  $expenseReport->getExpenseReportLines()[$i]->getId() . '.' . $logoUpload[0]->guessExtension();
                    $patho =  "build/expenseReport/".$expenseReport->getCode()."/";
                    $path =$patho.$document;
                 
                    
                    if (glob ($path)) {
                    $pathOld = $patho."old/".$document;
                        rename($path, $pathOld);
                    }
                    
                    $expenseReport->getExpenseReportLines()[$i]->setDocument($path);
                    try {
                        $logoUpload[0]->move(
                            $patho,
                            $document
                        );
                    } catch (FileException $e) {
                    }
                }
                
                $entityManager->persist($expenseReport->getExpenseReportLines()[$i]);
                $i++;
            }
            $entityManager->flush();
            if($customer->getBankDetails()[0]){
                if($customer->getBankDetails()[0]->getIBAN() and $customer->getBankDetails()[0]->getBIC()){
                    return $this->redirectToRoute('expenseReport_show', ['expenseReportID' => $expenseReport->getId()]);
                }
            }
            return $this->redirectToRoute('bankDetail_new', []);
        }

        return $this->render('Comptability/expense_report/new.html.twig', [
            'expenseReport' => $expenseReport,
            'form' => $form->createView(),
        ]);
       
    }

    #[Route('/edit/{expenseReportID}', name: 'edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(EntityManagerInterface $entityManager, $expenseReportID, Request $request): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));

        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportID);
        $user = $this->getUser();
        $customer = $user->getCustomer();
       

        
        if (($user == $expenseReport->getSupplier()->getCustomer()->getUser() or $this->isGranted("ROLE_TRESO")) and !$expenseReport->isComfirm()) {
            
            $form = $this->createForm(ExpenseReportType::class, $expenseReport);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
               
                $expenseReport->setDate(new DateTime());
                $expenseReport->setSupplier($customer->getSupplier());
               
                foreach ($expenseReport->getExpenseReportLines() as $expenseReportLine) {
                    $entityManager->persist($expenseReportLine);
                }

                foreach ($expenseReport->getExpenseReportRouteLines() as $expenseReportRouteLine) {
                    if ($expenseReportRouteLine->getDistance() <= 50) {
                        $expenseReportRouteLine->setAmount($expenseReportRouteLine->getDistance() * 0.15);
                    } else {
                        $expenseReportRouteLine->setAmount($expenseReportRouteLine->getDistance() * 0.20);
                    }
                    $entityManager->persist($expenseReportRouteLine);
                }

                $entityManager->persist($expenseReport);
                $entityManager->flush();

                $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportByCode($expenseReport->getCode());

                $i = 0;

                foreach ($form->get('expenseReportLines') as $expenseReportLine) {
                    $logoUpload = $expenseReportLine->get('document')->getData();
                    
                    if ($logoUpload) {
                    
                        //on créer le nouveau chemin du nouveau doc
                        $generalPath =  "build/expenseReport/".$expenseReport->getCode()."/";
                        $newDocument = 'expenseReportLineProof_' .  $expenseReport->getExpenseReportLines()[$i]->getId() . '.' . $logoUpload[0]->guessExtension();
                        $newPath =$generalPath.$newDocument;

                        //on recupere l'extension du doc à déplacer 
                        $oldPathOldDocument = $expenseReport->getExpenseReportLines()[$i]->getDocument();
                        $element = explode(".", $oldPathOldDocument);
                        $oldExtension = end($element);

                        //verif si le dossier old existe
                        if (!is_dir($generalPath.'old')) {
                            mkdir($generalPath.'old/');
                        }

                        if(glob($generalPath.'old/oldExpenseReportLineProof_'. $expenseReport->getExpenseReportLines()[$i]->getId()."_*")){
                            $listOldProof = glob($generalPath.'old/oldExpenseReportLineProof_'. $expenseReport->getExpenseReportLines()[$i]->getId()."_*");
                            $LastProof =end($listOldProof);
                            $listPartPathLastProof = explode("_", $LastProof);
                            $strNumberLastProof = end($listPartPathLastProof);
                            $intNumberLastProof = intval($strNumberLastProof);
                            $oldPath =$generalPath.'old/oldExpenseReportLineProof_'. $expenseReport->getExpenseReportLines()[$i]->getId()."_".$intNumberLastProof+1 . '.'.$oldExtension;
                            rename($newPath, $oldPath);
                        }else{
                            $oldPath =$generalPath.'old/oldExpenseReportLineProof_'. $expenseReport->getExpenseReportLines()[$i]->getId()."_1".'.'.$oldExtension;
                            rename($newPath, $oldPath);
                        }
                    
                        
                        //on stock le nouveau chemin 
                        $expenseReport->getExpenseReportLines()[$i]->setDocument($newPath);
                        try {
                            $logoUpload[0]->move(
                                $generalPath,
                                $newDocument
                            );
                        } catch (FileException $e) {
                        }
                    }
                    $entityManager->persist($expenseReport->getExpenseReportLines()[$i]);
                    $i++;
                }
                $entityManager->flush();

                if($customer->getBankDetails()[0]){
                    if($customer->getBankDetails()[0]->getIBAN() and $customer->getBankDetails()[0]->getBIC()){
                        return $this->redirectToRoute('expenseReport_show', ['expenseReportID' => $expenseReport->getId()]);
                    }
                }
                return $this->redirectToRoute('bankDetail_new', []);
            }

            return $this->render('Comptability/expense_report/edit.html.twig', [
                'expenseReport' => $expenseReport,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('account');
        }
    }

    #[Route('/show/{expenseReportID}', name: 'show')]
    #[IsGranted('ROLE_USER')]
    public function show(EntityManagerInterface $entityManager, $expenseReportID): Response
    {
         $user = $this->getUser();
        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findExpenseReportById($expenseReportID);
        if ($user == $expenseReport->getSupplier()->getCustomer()->getUser() or $this->isGranted("ROLE_TRESO")) {
        return $this->render('Comptability/expense_report/show.html.twig', [
            'expenseReport' => $expenseReport,
        ]);
        } else {
            return $this->redirectToRoute('account');
        }
    }

    

    #[Route('/comfirm/{expenseReportID}', name: 'comfirm')]
    #[IsGranted('ROLE_TRESO')]
    public function comfirm(EntityManagerInterface $entityManager, Request $request, $expenseReportID, SesClient $ses): Response
    {
        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findById($expenseReportID)[0];

        $expenseReport->setComfirm(true);
       

        $transaction = new Transaction();
        $transaction->setExercice($expenseReport->getExercice());
        $expenseReport->setTransaction($transaction);
        $transaction->setQuote('NDF-'.$expenseReport->getCode());
        if (isset($entityManager->getRepository(Transaction::class)->findMaxDayTransaction(date("Ymd") * 100)[0]['code'])) {
            $nbtransaction = $entityManager->getRepository(Transaction::class)->findMaxDayTransaction(date("Ymd") * 100)[0]['code'];
        } else {
            $nbtransaction = 0;
        }
        $transaction->setCode(intval(date("Ymd")) * 100 + $nbtransaction + 1);
        $transaction->setClosure(false);


        $transactionline = new TransactionLine();
        $transactionline->setQuote('NDF-'.$expenseReport->getCode());
        $transactionline->setTransaction($transaction);
        $transactionline->setDate(new \DateTime());
        $transactionline->setAmount(ExpenseReportController::expenseReportTotale($expenseReport));
        $transactionline->setLabel("NDF-" . $expenseReport->getCode());

        $entityManager->persist($transaction);
        $entityManager->persist($transactionline);
        $entityManager->persist($expenseReport);
        $entityManager->flush();

                $sender_email = 'no-reply@*****.net';
        $recipient_emails = [$expenseReport->getSupplier()->getCustomer()->getUser()->getEmail()];

        $subject = 'Merer - Confirmation NDF';
        $plaintext_body = 'Confirmation NDF' ;
        $char_set = 'UTF-8';
        $result = $ses->sendEmail([
            'Destination' => [
                'ToAddresses' => $recipient_emails,
            ],
            'ReplyToAddresses' => [$sender_email],
            'Source' => $sender_email,
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => $char_set,
                        'Data' =>$this->renderView('emails/confirm_ndf.html.twig',["expenseReport" => $expenseReport])
                    ],
                    'Text' => [
                        'Charset' => $char_set,
                        'Data' => $plaintext_body,
                    ],
                ],
                'Subject' => [
                    'Charset' => $char_set,
                    'Data' => $subject,
                ],
            ],
        ]);

        return $this->redirectToRoute('expenseReport_showAll');
    }

    #[Route('/QRCodeGen', name: 'qrcode')]
    #[IsGranted('ROLE_BF')]
    public function qrCode()
    {
        $writer = new PngWriter();
        $qrCode = QrCode::create('https://15.236.191.187/expenseReport/new')
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(120)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $logo = Logo::create('build/images/logo.png')
        ->setResizeToWidth(60);
        $label = Label::create('NDF')->setFont(new NotoSans(8));

        $qrCodes = [];;

        $qrCode->setSize(400)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
        $qrCodes['withImage'] = $writer->write(
            $qrCode,
            $logo,
            $label->setText('NDF')->setFont(new NotoSans(20))
        )->getDataUri();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $dompdf->set_option('isHtml5ParserEnabled', true);

        $html = $this->renderView('Comptability/expense_report/expenseReportQRCodePDF.html.twig', [

            'qrCodes' => $qrCodes
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("NDFQRCode.pdf", [
            "Attachment" => true
        ]);
    }

     #[Route('/pdf/{expenseReportID}', name: 'expenseReportPDF')]
    #[IsGranted('ROLE_TRESO')]
    public function expenseReportPDF(EntityManagerInterface $entityManager, $expenseReportID){
        $expenseReport = $entityManager->getRepository(ExpenseReport::class)->findById($expenseReportID)[0];
        
       
        $federation = $entityManager->getRepository(Federation::class)->findBySocialReason("******")[0];
     
        $institution = $entityManager->getRepository(Institution::class)->findHeadquarterById($federation->getId());

        $options = new Options();
        $options->set('defaultFont', 'Roboto');
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);
     $html = $this->renderView('Comptability/expense_report/templateExpenseReport.html.twig', [
            'expenseReport' => $expenseReport,
            'federation'=>$federation,
            'institution'=>$institution,
        ]);


        $dompdf->loadHtml($html);


        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        //file_put_contents("../files/invoices/Fac-".$invoice->getCode().".pdf", $dompdf->output());
        $dompdf->stream("Fac-".$expenseReport->getCode(), [
            "Attachment" => false
        ]);

        exit(0);
    }
    public function expenseReportTotale($expenseReport)
    {
        $nbTrajet = count($expenseReport->getExpenseReportRouteLines());
        $nbFrais = count($expenseReport->getExpenseReportLines());
        $totale = 0;

        for ($i = 0; $i < $nbTrajet-1; $i++) {

            if ($expenseReport->getExpenseReportRouteLines()[$i]->getAmount() != null and  $expenseReport->getExpenseReportRouteLines()[$i]->getRepayGrid()->getAmount() == null ){  
                $add = $expenseReport->getExpenseReportRouteLines()[$i]->getAmount();
            } else if($expenseReport->getExpenseReportRouteLines()[$i]->getAmount() == null and  $expenseReport->getExpenseReportRouteLines()[$i]->getRepayGrid()->getAmount() != null){
                $add = $expenseReport->getExpenseReportRouteLines()[$i]->getRepayGrid()->getAmount();
            } else {
                $add = 0;
            }

            $totale += $add;
            
        }

        for ($i = 0; $i < $nbFrais; $i++) {
            if ($expenseReport->getExpenseReportLines()[$i]->getAmount()) {
                $totale += $expenseReport->getExpenseReportLines()[$i]->getAmount();
            }
            
        }

        return $totale;
    }

}

