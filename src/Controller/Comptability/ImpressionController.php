<?php

namespace App\Controller\Comptability;

use DateTime;
use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Comptability\Event;
use Aws\Ses\SesClient;
use App\Entity\Comptability\Invoice;
use App\Entity\Comptability\Customer;
use App\Entity\Comptability\Exercice;
use App\Entity\Comptability\Impression;
use Endroid\QrCode\QrCode;
use App\Entity\Comptability\InvoiceLine;
use App\Form\Comptability\ImpressionType;
use Endroid\QrCode\Logo\Logo;
use App\Entity\Comptability\CatalogService;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use App\Form\Comptability\ImpressionForBFType;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Font\NotoSans;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/impression', name: 'impression_')]
class ImpressionController extends AbstractController
{
    #[Route('/QRCodeGen', name: 'qrcode')]
    #[IsGranted('ROLE_BF')]
    public function qrCode()
    {
       
       
            $writer = new PngWriter();
            $qrCode = QrCode::create('https://15.236.191.187/Comptability/impression/new')
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(120)
                ->setMargin(0)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
            $logo = Logo::create('build/images/logo.png')
            ->setResizeToWidth(60);
            $label = Label::create('impression')->setFont(new NotoSans(8));

            $qrCodes = [];;

            $qrCode->setSize(400)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
            $qrCodes['withImage'] = $writer->write(
                $qrCode,
                $logo,
                $label->setText('impression')->setFont(new NotoSans(20))
            )->getDataUri();

            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($pdfOptions);

            $dompdf->set_option('isHtml5ParserEnabled', true);

            $html = $this->renderView('Comptability/impression/ImpressionQRCodePDF.html.twig', [
                
                'qrCodes' => $qrCodes
            ]);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            $dompdf->stream("ImpressionQRCode.pdf", [
                "Attachment" => true
            ]);
        
    }


   

    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_BF')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $impressions = $entityManager->getRepository(Impression::class)->findAllinOrder();

         if ($request->isMethod('post')) {
             $posts = $request->request->all();
             if ($posts['customer']) {
                 $impressions = array_intersect($impressions, $entityManager->getRepository(Impression::class)->findAllByCustomer($posts['customer']));
             }
            if ($posts['format'] && $posts['format'] != 'null') {
                $impressions = array_intersect($impressions, $entityManager->getRepository(Impression::class)->findAllByFormat($posts['format']));
            }
             if ($posts['rectoVerso'] && $posts['rectoVerso'] != 'null') {
                 $impressions = array_intersect($impressions, $entityManager->getRepository(Impression::class)->findAllIfRV($posts['rectoVerso']));
             }
            if ($posts['couleur'] && $posts['couleur'] != 'null') {
                $impressions = array_intersect($impressions, $entityManager->getRepository(Impression::class)->findAllIfCouleur($posts['couleur']));
            }
         }

        return $this->render('Comptability/impression/showAll.html.twig', [
            'impressions' => $impressions,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(EntityManagerInterface $entityManager, Request $request, SesClient $ses): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));

        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $impression = new Impression();
        $form = $this->createForm(ImpressionType::class, $impression, ['liste_customer' =>  $entityManager->getRepository(Customer::class)->findAllImpressionAccess()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $impression->setDatetime(new DateTime());
          
            $impression->setExercice($exercice);
            $invoiceLine = new InvoiceLine();
            $invoiceLine->setQuantity($impression->getQuantite());
            if($impression->isFactureFinDuMois()){ //si facture fin mois
                    
                if($impression->getFormat() == 'plastification'){
                    $impression->setCouleur(false);
                    $impression->setRectoVerso(false);
                    $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode('plastification'));
                
                } else {
                    if($impression->isRectoVerso()){
                        if($impression->isCouleur()){
                            $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat().'_RV'.'_couleur'));
                        }else{
                            $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . '_RV'));
                        }
                    } else {
                        if ($impression->isCouleur()) {
                            $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() .'_R' . '_couleur'));
                        } else {
                            $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . '_R'));
                        }
                    }
                }
                $invoice = $entityManager->getRepository(Invoice::class)->findCurrentInvoiceImpressionOfCustomer($impression->getCustomer());
                
                if($invoice == Null){
                    $invoice = new Invoice();
                    $invoice->setExercice($exercice);
                    $invoice->setAcquitted(false);
                    $invoice->setComfirm(false);
                    $invoice->setReady(false);
                    $invoice->setCategory('Impression');
                    $invoice->setCode('Impress_'.$impression->getCustomer()->getName().date('m'));
                    $invoice->setCustomer($impression->getCustomer());
                    $invoice->setCreationDate(new Datetime());
                }
            
            }else{ // donc facture direct 
                if ($impression->getFormat() == 'plastification') {
                    $impression->setCouleur(false);
                    $impression->setRectoVerso(false);
                    $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode('plastification'));
                } else {
                    if ($impression->isRectoVerso()) {
                        if ($impression->isCouleur()) {
                            $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . 'RV' . 'couleur'));
                        } else {
                            $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . 'RV'));
                        }
                    } else {
                        if ($impression->isCouleur()) {
                            $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . 'R' . 'couleur'));
                        } else {
                            $invoiceLine->setCatalogService($entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . 'R'));
                        }
                    }
                }

                $invoice = new Invoice();
                $invoice->setExercice($exercice);
                $invoice->setCategory('Impression');
                $invoice->setCode('impressionDirect'.$impression->getCustomer()->getName() . date('d/m/y h:i'));
                $invoice->setCustomer($impression->getCustomer());
                $invoice->setCreationDate(new Datetime());
                
                $invoice->setAcquitted(false);
                $invoice->setComfirm(false);
                $invoice->setReady(false);
                
            }
                $impression->setInvoice($invoice);
            $invoiceLine->setInvoice($invoice);
            $entityManager->persist($invoice);
            $entityManager->persist($invoiceLine);
           
            $entityManager->persist($impression);
            $entityManager->flush();
            
            try{
                $sender_email ='no-reply@*****.net';
                $recipient_emails = [$impression->getCustomer()->getUser()->getEmail()];

                $subject = 'Merer - Nouvelle impression';
                $plaintext_body = 'Nouvelle impression' ;
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
                                'Data' =>$this->renderView('emails/impression.html.twig',["impression" => $impression])
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
            }catch(Exception $e){

            }finally{
                return $this->redirectToRoute('impression_validation', []);
            }    
        }

        return $this->render('Comptability/impression/new.html.twig', [
            'impression' => $impression,
            'form' => $form->createView(),

        ]);
    }


     #[Route('/newForBF', name: 'newForBF')]
      #[IsGranted('ROLE_BF')]
    public function newForBF(EntityManagerInterface $entityManager, Request $request): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));

        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $impression = new Impression();
        $form = $this->createForm(ImpressionForBFType::class, $impression, ['liste_event' =>  $entityManager->getRepository(Event::class)->findAllByExercice($exercice->getAnnee())]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $impression->setDatetime(new DateTime());
          
            $impression->setExercice($exercice);
    
            $entityManager->persist($impression);
            $entityManager->flush();
            
          
            return $this->redirectToRoute('impression_validation', []);
            

            
        }
        return $this->render('Comptability/impression/newForBF.html.twig', [
            'impression' => $impression,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/validation', name: 'validation')]
    public function validation(): Response
    {
        return $this->render('Comptability/impression/validation.html.twig', [
        ]);
    }

    #[Route('/delete/{impressionId}', name: 'delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $entityManager, $impressionId): Response
    {

        $impression = $entityManager->getRepository(Impression::class)->findById($impressionId);

        $customers = $entityManager->getRepository(Customer::class)->findAll();
        $i = 0;
        while (!isset($customer) and isset($customers[$i])) {
            if ($customers[$i]->getUser() == $this->getUser()) {
                $customer = $customers[$i];
            }
            $i++;
        }
        if ($customer == $impression->getCustomer() or $this->isGranted("ROLE_TRESO")) {
            $entityManager->remove($impression);
            $entityManager->flush();

        return $this->redirectToRoute('impression_showAll', []);
        } else {
            return $this->redirectToRoute('account');
        }
    }

     public function impressionTotale(EntityManagerInterface $entityManager, $impressionId)
    {
         $impression = $entityManager->getRepository(Impression::class)->findById($impressionId);
        $totale = 0;

        
                    
        if ($impression->getFormat() == 'plastification') {
            return $impression->getQuantite() * $entityManager->getRepository(CatalogService::class)->findByCode('plastification')->getAmountTtc();
        } else {
            if ($impression->isRectoVerso()) {
                if ($impression->isCouleur()) {
                     return $impression->getQuantite() * $entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . '_RV_' . 'couleur')->getAmountTtc();
                } else {
                   return  $impression->getQuantite() * $entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . '_RV')->getAmountTtc();
                }
            } else {
                if ($impression->isCouleur()) {
                  return   $impression->getQuantite() * $entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . '_R_' . 'couleur')->getAmountTtc();
                } else {
                   return  $impression->getQuantite() * $entityManager->getRepository(CatalogService::class)->findByCode($impression->getFormat() . '_R_')->getAmountTtc();
                }
            }
        }


    }

}
