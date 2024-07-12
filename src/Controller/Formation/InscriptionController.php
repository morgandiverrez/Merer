<?php

namespace App\Controller\Formation;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Formation\Profil;
use App\Entity\Formation\Seance;
use Aws\Ses\SesClient;
use App\Entity\Formation\Evenement;
use Endroid\QrCode\QrCode;
use App\Entity\Formation\SeanceProfil;
use App\Form\Formation\PonctuelleType;
use App\Form\Formation\InscriptionType;
use Endroid\QrCode\Logo\Logo;
use App\Form\Formation\SeanceProfilType;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
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

#[Route('/inscription', name: 'inscription_')]
class InscriptionController extends AbstractController
{

    #[Route('/{seanceID}', name: 'general')]
    #[IsGranted('ROLE_USER')]
    public function distribution(EntityManagerInterface $entityManager, Request $request, $seanceID): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findByID($seanceID)[0];
        if(empty($seance->getEvenement())){
            return $this->redirectToRoute('inscription_ponctuelle', ['seanceID' => $seance->getId()]);
        }else{
             return $this->redirectToRoute('inscription_evenement', ['evenementID' => $seance->getEvenement()->getId()]);
        }
    }

    #[Route('/ponctuelle/{seanceID}', name: 'ponctuelle')]
    #[IsGranted('ROLE_USER')]
    public function ponctuelle(EntityManagerInterface $entityManager, Request $request, $seanceID, SesClient $ses): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findByID($seanceID)[0];
        $profil = $this->getUser()->getProfil();
        // on verifie que la seance est sensé etre visible (sinon, on renvoi vers le profil de l'utilisateur)
        if (! $seance->isVisible()) {
            return $this->redirectToRoute('profil_show', []);
        }

        //on verifie que cette seance n'est pas dans un evenement (si oui, on renvoie vers le formulaire approprié)
        if( null != $seance->getEvenement()){
            return $this->redirectToRoute('inscription_evenement', ['evenementID' => $seance->getEvenement()->getId()]);
        }

        //on verifie qu'il reste des places
        
        if( $seance->getNombrePlace() <= $entityManager->getRepository(SeanceProfil::class)->CountBySeance($seance)[0][1]){
            return $this->render('Formation_/inscription/noPlace.html.twig');
        }

        if($seance->getDatetime() <= date('d/m/y H:i:s')){
            return $this->render('Formation_/inscription/close.html.twig');
        }

        if($entityManager->getRepository(SeanceProfil::class)->findBy2ID($seance->getId(), $profil->getId())) {
            $seanceProfil = $entityManager->getRepository(SeanceProfil::class)->findBy2ID($seance->getId(), $profil->getId())[0];
        }else{
            $seanceProfil = new SeanceProfil();
        }
        

        $form = $this->createForm(InscriptionType::class, $seanceProfil, [ 'liste_lieu' => $seance->getLieux()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
           
            $seanceProfil->setHorrodateur(new DateTime());
            $profil->addSeanceProfil($seanceProfil);
            $seance = $entityManager->getRepository(Seance::class)->findByID(strval($seanceID))[0];
            $seance->addSeanceProfil($seanceProfil);
            $entityManager->persist($seanceProfil);
            $entityManager->flush();

            // Replace sender@example.com with your "From" address.
            // This address must be verified with Amazon SES.
            $sender_email = 'no-reply@*****.net';

            // Replace these sample addresses with the addresses of your recipients. If
            // your account is still in the sandbox, these addresses must be verified.
            $recipient_emails = [$this->getUser()->getEmail()];

            $subject = 'Merer - Inscription Formation';
            $plaintext_body = 'Inscription Formation' ;
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
                        'Data' =>$this->renderView('emails/inscription_seance.html.twig',["seance" => $seance, "inscription" => $seanceProfil])
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

            return $this->redirectToRoute('seance_show', ['seanceID' => $seance->getID()]);
        }

        return $this->render('Formation_/inscription/ponctuelle.html.twig', [
            'seanceProfil' => $seanceProfil,
            'form' => $form->createView(),
            'seance' => $seance ,
        ]);
    }

    #[Route('/evenement/{evenementID}', name: 'evenement')]
    #[IsGranted('ROLE_USER')]
    public function evenement( EntityManagerInterface $entityManager, Request $request, $evenementID, SesClient $ses): Response
    {
        $seanceByCreneauAndParcours = [];
        $restePlace =[];
        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
        //on verifie que l'evenement est visible
        if ( ! $evenement->isVisible()) {
            return $this->redirectToRoute('profil_show', []);
        }
         // on verifie qu'il reste des places
        if (  $entityManager->getRepository(SeanceProfil::class)->CountByEvenement($evenement)[0][1] >= $evenement->getNombrePlace()) {
            return $this->render('Formation_/inscription/noPlace.html.twig');
        }

        //on verifie que les inscriptions ne sont pas cloturé
        if ($evenement->getDateFinInscription() <= date('y/m/d H:i:s')) {
            return $this->render('Formation_/inscription/close.html.twig');
        }

        $seances = $evenement->getSeances(); //on recup tt les seances qui ont un groupe qui commence par la variable groupe

       

        foreach($seances as $seance){   
            //rangé par parcours et horaire
            if( $seance->getParcours() != null ){ 
                $seanceByCreneauAndParcours[strval($seance->getDatetime()->format("d/m/Y H:i"))][$seance->getParcours()] = $seance;
            }else{ //si pas de parcours (donc formation pour tt les parcours si plusieur parcours)
                foreach($evenement->getParcours() as $parcours){ // on itere les parcours pour les remplir tous de cette seance
                    $seanceByCreneauAndParcours[$seance->getDatetime()->format("d/m/Y H:i")][$parcours] = $seance;
                }
            }

            // verifié s'il reste des places
            if ($entityManager->getRepository(SeanceProfil::class)->CountBySeance($seance) < $seance->getNombrePlace() ) {
                $restePlace[$seance->getId()] = false;
            }else{
                $restePlace[$seance->getId()] = true;
            }
        }

        $user = $this->getUser(); //on recup l'user 
        $profil = $user->getProfil();
        


        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if($evenement->isParcoursObligatoire()){
                 $parcoursSeance = $entityManager->getRepository(Seance::class)->findAllByParcours($evenement, $posts['inscription']);
                 foreach( $parcoursSeance as $seance){
                    $seanceProfil = new SeanceProfil();
                    $seanceProfil->setSeance($seance);
                    $seanceProfil->setProfil($profil);
                    $seanceProfil->setHorrodateur(new DateTime());
                    $seanceProfil->setAttente($posts['attentes_' . $seance->getId()]);
                    $seanceProfil->setLieu($evenement->getLieu());
                    if($evenement->isAutorisationPhoto()){
                        $seanceProfil->isAutorisationPhoto($posts['autorisation_photo']);
                    }
                    if($evenement->getModePaiement() != []){
                        $seanceProfil->setModePaiement($posts['mode_paiement']);
                    }
                    if($evenement->isCovoiturage()){
                        $seanceProfil->setCovoiturage($posts['participation_covoiturage']);
                    }
                    $entityManager->persist($seanceProfil);
                }
            }else{
                foreach ($seances as $seance) {
                    if (isset($posts['inscription_' . $seance->getDatetime()->format('d/m/y H:i:s')])  and $posts['inscription_' . $seance->getDatetime()->format('d/m/y H:i:s')] == $seance->getId()) {
                        $seanceProfil = new SeanceProfil();
                        $seanceProfil->setSeance($seance);
                        $seanceProfil->setProfil($profil);
                        $seanceProfil->setHorrodateur(new DateTime());
                        $seanceProfil->setAttente($posts['attentes_'.$seance->getId()]);
                        $seanceProfil->setLieu($seance->getEvenement()->getLieu());
                        $seanceProfil->isAutorisationPhoto($posts['autorisation_photo']);
                        $seanceProfil->setModePaiement($posts['mode_paiement']);
                        $seanceProfil->setCovoiturage($posts['participation_covoiturage']);
                        $entityManager->persist($seanceProfil);
                    }
                }
            }
            $entityManager->flush();

            $sender_email = 'no-reply@*****.net';
            $recipient_emails = [$user->getEmail()];

            $subject = 'Merer - Inscription Formation';
            $plaintext_body = 'Inscription Formation' ;
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
                        'Data' => $this->renderView('emails/inscription_event.html.twig',["evenement" => $evenement, "posts" => $posts])
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

            if($evenement->getURL() != null){
                return  $this->redirect($evenement->getURL());
               
            }else {
                return  $this->redirectToRoute('profil_show', []);
            }
           
            
        }

       
        return $this->render('Formation_/inscription/WEF.html.twig', [
            'seances'=> $seances,
            'evenement'=>$evenement,
            'seanceByCreneauAndParcours'=> $seanceByCreneauAndParcours,
           'restePlace'=> $restePlace,
        ]);
        
    }
   

    #[Route('/delete/{seanceID}/{profilID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $seanceID, $profilID): Response
    {
        
        $seanceProfil = $entityManager->getRepository(SeanceProfil::class)->findBy2Id($seanceID, $profilID)[0];
        $entityManager->remove($seanceProfil);
        $entityManager->flush();

        return $this->redirectToRoute('seance_liste_inscrit', ['seanceID' => $seanceID]);
    }

    #[Route('/QRCodeEventGen/{evenementID}', name: 'qrcodeEvent')]
    #[IsGranted('ROLE_BF')]
    public function qrCodeEvent(EntityManagerInterface $entityManager, $evenementID)
    {$seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
      
        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
        $seance = $evenement->getSeances()[0];
        return $this->redirectToRoute('inscription_qrcode', ['seanceID' => $seance->getId() ]);
    }


    #[Route('/QRCodeGen/{seanceID}', name: 'qrcode')]
    #[IsGranted('ROLE_BF')]
    public function qrCode(EntityManagerInterface $entityManager, $seanceID)
    {$seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
      
        $seance = $entityManager->getRepository(Seance::class)->findByID($seanceID)[0];
        if (empty($seance->getEvenement())) {
            $writer = new PngWriter();
            $qrCode = QrCode::create('https://15.236.191.187/inscriptionFormation_//'. $seanceID)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(120)
                ->setMargin(0)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
            $logo = Logo::create('build/images/logo.png')
                ->setResizeToWidth(60);
            $label = Label::create('inscription')->setFont(new NotoSans(8));

            $qrCodes = [];;
           
           

            $qrCode->setSize(400)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
            $qrCodes['withImage'] = $writer->write(
                $qrCode,
                $logo,
                $label->setText('Inscription')->setFont(new NotoSans(20))
            )->getDataUri();

            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($pdfOptions);

            $dompdf->set_option('isHtml5ParserEnabled', true);

            $html = $this->renderView('Formation_/inscription/InscriptionQRCodePDF.html.twig', [
                'seance'=> $seance,
                'qrCodes' => $qrCodes]);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            $dompdf->stream("InscriptionQRCode".$seance->getName().".pdf", [
                "Attachment" => true
            ]);
            
        } else {
            $writer = new PngWriter();
            $qrCode = QrCode::create('https://15.236.191.187/inscriptionFormation_//' . $seanceID)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(120)
                ->setMargin(0)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
            $logo = Logo::create('build/images/logo.png')
                ->setResizeToWidth(60);
            $label = Label::create('inscription')->setFont(new NotoSans(8));

            $qrCodes = [];;

            $qrCode->setSize(400)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
            $qrCodes['withImage'] = $writer->write(
                $qrCode,
                $logo,
                $label->setText('Inscription')->setFont(new NotoSans(20))
            )->getDataUri();

            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($pdfOptions);

            $dompdf->set_option('isHtml5ParserEnabled', true);

            $html = $this->renderView('Formation_/inscription/InscriptionQRCodePDF.html.twig', [
                'seance' => $seance,
                'qrCodes' => $qrCodes
            ]);

            $dompdf->loadHtml($html);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            $dompdf->stream("InscriptionQRCode".$seance->getEvenement()->getName().".pdf", [
                "Attachment" => true
            ]);
        }
    }

    

} 


