<?php

namespace App\Controller\Formation;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Formation\Profil;
use App\Entity\Formation\Retour;
use App\Entity\Formation\Seance;
use App\Form\Formation\RetourType;
use App\Entity\Formation\Evenement;
use Endroid\QrCode\QrCode;
use App\Form\Formation\RetourEventType;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Symfony\UX\Chartjs\Model\Chart;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Font\NotoSans;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/retour', name: 'retour_')]

class RetourController extends AbstractController
{
    #[Route('/new/{seanceID}', name: 'new')]
    #[IsGranted('ROLE_USER')]
    public function new(EntityManagerInterface $entityManager, Request $request, $seanceID): Response
    {
        $retour = new Retour();

        $seance = $entityManager->getRepository(Seance::class)->findByID(strval($seanceID))[0];
        $retour->setSeance($seance);

        $user = $this->getUser();
        $profil = $user->getProfil();
        $retour->setProfil($profil);
        
        $form = $this->createForm(RetourType::class, $retour);
        $form->handleRequest($request);
       

        if ($form->isSubmitted() && $form->isValid()) {

            if($seance->getFormation()->getBadge()){
                $profil -> addBadge($seance-> getFormation() -> getBadge());
            }
            $entityManager->persist($retour);
            $entityManager->persist($profil);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show', []);
        }

        return $this->render('retour/new.html.twig', [
            'retour' => $retour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/newEvent/{evenementID}', name: 'newEvent')]
    #[IsGranted('ROLE_USER')]
    public function newEvent(EntityManagerInterface $entityManager, Request $request, $evenementID): Response
    {
        $retour = new Retour();

        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
        

        $user = $this->getUser();
        $profil = $user->getProfil();
        $retour->setProfil($profil);

        $form = $this->createForm(RetourEventType::class, $retour, ['liste_seance' => $evenement->getSeances()]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($retour->getSeance()->getFormation()->getBadge()) {
                $profil->addBadge($retour->getSeance()->getFormation()->getBadge());
            }
            $entityManager->persist($retour);
            $entityManager->persist($profil);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show', []);
        }

        return $this->render('retour/newEvent.html.twig', [
            'retour' => $retour,
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }


     #[Route('/sdf', name: 'sdf')]
    #[IsGranted('ROLE_FORMA')]
    public function sdf(EntityManagerInterface $entityManager){
        $seances = $entityManager->getRepository(Seance::class)->findAllByYear(date("Y")."-01-01", strval(date("Y"))."-12-31");
        $inputFileName = 'C:\wamp64\www\Merer\public\public\files\SDF\SDF_vierge.xlsx';
        /** Load $inputFileName to a Spreadsheet object **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        foreach( $seances as $seance){
            $clonedWorksheet = clone $spreadsheet->getSheetByName('Modèle vierge'); //créer une nouvelle feuille
            $clonedWorksheet->setTitle($seance->getName() . strval($seance->getDatetime()->format("Y-m-d"))); //on nomme la nouvelle feuille
            $spreadsheet->addSheet($clonedWorksheet); // on ajoute au classeur la nouvelle feuille
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx"); // on ouvre en ecriture le classeur

            $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell('F7')->setValue($seance->getName()); // inscription intitulé formation
            $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell('L8')->setValue($seance->getDatetime()->format("Y-m-d")); // on met la date sur la feuille active
            $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell('N10')->setValue($seance->getEvenement()->getName()); // 

            $i = 8;
            foreach( $seance->getProfil() as $formateurice){
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell('F'.$i)->setValue($formateurice->getName()." ".$formateurice->getLastname());
                $i++;
            }

            $i = 10;
            foreach ($seance->getLieux() as $lieu) {
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell('F'.$i)->setValue($lieu->getName());
                $i++;
            }
            
            $colonne = ['G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE'];
            $i = 0;
            foreach( $seance->getRetour() as $retour){
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell($colonne[$i].'15')->setValue( strval($retour->getNoteContenu()));
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell($colonne[$i].'16')->setValue( strval($retour->getNoteAnimation()));
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell($colonne[$i].'17')->setValue( strval($retour->getNoteImplication()));
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell($colonne[$i].'18')->setValue( strval($retour->getNoteReponseAtente()));
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell($colonne[$i].'19')->setValue(strval($retour->getNoteNivCompetence()));
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell($colonne[$i].'20')->setValue( strval($retour->getNoteUtilite()));
                $spreadsheet->getSheetByName($clonedWorksheet->getTitle())->getCell($colonne[$i].'21')->setValue( strval($retour->getNoteGenerale()));
                $i++;
            }
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        echo ("test");
        $writer->save("SDF_" . date("Y").".xlsx");
        echo("test");
        $finaleFile = "public\SDF_". date("Y")."xlsx";
        echo($finaleFile);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($finaleFile) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($finaleFile));
        readfile($finaleFile);


        
        return $this->redirectToRoute('profil_show', []);
        
        
    }

    #[Route('/resultat/{seanceID}', name: 'resultat')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function resultat(ChartBuilderInterface $chartBuilder, EntityManagerInterface $entityManager, $seanceID): Response
    {

        $seance = $entityManager->getRepository(Seance::class)->findByID($seanceID);
        $retours = $entityManager->getRepository(Retour::class)->findBySeance($seance);

        $dataSomme=array([0, 0, 0, 0, 0, 0, 0], 
                         [0, 0, 0, 0, 0, 0, 0],
                         [0, 0, 0, 0, 0, 0, 0],
                         [0, 0, 0, 0, 0, 0, 0],
                         [0, 0, 0, 0, 0, 0, 0]);
       
        $dataMoyenne = array(0, 0, 0, 0, 0, 0, 0);

        $dataApport = array(0, 0, 0, 0);

        $dataRemarque = array('','','','','','','','','','','',''); 
        
        $dataQuestion = array(
            'Satisfaction générale vis-à-vis de la formation', 
            'Pensez-vous que les compétences acquises durant cette formation seront utiles dans votre parcours de associatif ?', 
            'Comment évaluez-vous le niveau de compétences techniques (connaissances du sujet, exposé) et pédagogiques (débit de parole, charisme, échange) des formateurices ?', 
            'Cette formation a-t-elle répondu à vos attentes ?',
            'Comment évaluez-vous l\'implication des participant.e.s à la formation ?',
            'Comment évaluez-vous l’animation [séquence, utilisation du matériel, débats] de la formation ?',
            'Comment évaluez-vous le contenu de la formation (informations adaptées au public, compréhension, technicité) ?', 
            'Qu\'est-ce que tu as aimé ? ', 
            'Qu\'est-ce que tu as moins aimé ?',
            'Qu\'est-ce que tu aurais aimé voir dans cette formation ?',
            'Mot de la fin');
       
        foreach( $retours as $retour){ // indentation pour passer par chaque retour
            $note = $retour ->getNoteGenerale();
            $critere = 0;
            $dataSomme[$note - 1][$critere] += 1; // ajout d'un 1 en fonction de la valeur du retour
            $dataMoyenne[$critere] += $note;
            
            $remarque = $retour->getRemarqueGenerale();
            $dataRemarque[$critere] .= ',' . $remarque;
        
            // indentation pour passer par chaque retour
            $note = $retour->getNoteUtilite();
            $critere = 1;
            $dataSomme[$note - 1][$critere] += 1; // ajout d'un 1 en fonction de la valeur du retour
            $dataMoyenne[$critere] += $note;

            $remarque = $retour->getRemarqueUtilite();
            $dataRemarque[$critere] .= ',' . $remarque;
        
            // indentation pour passer par chaque retour
            $note = $retour->getNoteNivCompetence();
            $critere = 2;
            $dataSomme[$note - 1][$critere] += 1; // ajout d'un 1 en fonction de la valeur du retour
            $dataMoyenne[$critere] += $note;

            $remarque = $retour->getRemarqueNivCompetence();
            $dataRemarque[$critere] .= ',' . $remarque;
        
            // indentation pour passer par chaque retour
            $note = $retour->getNoteReponseAtente();
            $critere = 3;
            $dataSomme[$note - 1][$critere] += 1; // ajout d'un 1 en fonction de la valeur du retour
            $dataMoyenne[$critere] += $note;

            $remarque = $retour->getRemarqueReponseAttente();
            $dataRemarque[$critere] .= ',' . $remarque;
        
            // indentation pour passer par chaque retour
            $note = $retour->getNoteImplication();
            $critere = 4;
            $dataSomme[$note - 1][$critere] += 1; // ajout d'un 1 en fonction de la valeur du retour
            $dataMoyenne[$critere] += $note;

            $remarque = $retour->getRemarqueImplication();
            $dataRemarque[$critere] .= ',' . $remarque;
        
                                // indentation pour passer par chaque retour
            $note = $retour->getNoteAnimation();
            $critere = 5;
            $dataSomme[$note - 1][$critere] += 1; // ajout d'un 1 en fonction de la valeur du retour
            $dataMoyenne[$critere] += $note;


            $remarque = $retour->getRemarqueAnimation();
            $dataRemarque[$critere] .= ',' . $remarque;
       
            // indentation pour passer par chaque retour
            $note = $retour->getNoteContenu();
            $critere = 6;
            $dataSomme[$note - 1][$critere] += 1; // ajout d'un 1 en fonction de la valeur du retour
            $dataMoyenne[$critere] += $note;

            $remarque = $retour->getRemarqueContenu();
            $dataRemarque[$critere] .= ',' . $remarque;
        
        
            $remarque = $retour->getPlusAimer();
            $dataRemarque[7] .= ',' . $remarque;

            $remarque = $retour->getMoinsAimer();
            $dataRemarque[8] .= ',' . $remarque;

            $remarque = $retour->getAimerVoir();
            $dataRemarque[9] .= ',' . $remarque;

            $remarque = $retour->getMotFin();
            $dataRemarque[10] .= ',' . $remarque;
        
            // indentation pour passer par chaque retour
            $choix = $retour->getApportGenerale();

            switch ($choix) {
                case ('ne m\'a rien apporté'):
                    $dataApport[0] += 1; // ajout d'un 1 en fonction de la valeur du retour
                   
                    break;
                case ('a confirmé ce que je savais déjà'):
                    $dataApport[1] += 1; // ajout d'un 1 en fonction de la valeur du retour
                   
                    break;
                case ('m\'a apporté de nouvelles connaissances'):
                    $dataApport[2] += 1; // ajout d'un 1 en fonction de la valeur du retour
                   
                    break;
                case ('m\'a permis d`\'échanger avec les participant.e.s'):
                    $dataApport[3] += 1; // ajout d'un 1 en fonction de la valeur du retour
                   
                    break;
                
            }
            
           
        }
        
        
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        $chartApport = $chartBuilder->createChart(Chart::TYPE_BAR);

        $chart->setData([
            'labels' => [   'Satisfaction générale vis-à-vis de la formation',
                            'Utilité des compétences acquises', 
                            'Compétences techniques et pédagogiques des formateurices', 
                            'Cette formation a-t-elle répondu à vos attentes ?', 
                            'L\'implication des participant.e.s', 
                            'L\'animation de la formation',
                            'Le contenu de la formation'],
            'datasets' => [
                [   'label' => 'Mauvais',
                    'backgroundColor' => 'blue',
                    'stack' => 'Stack 0',
                    'data' => $dataSomme[0],
                ],
                [   'label' => 'Moyen',
                    'backgroundColor' => 'red',
                    'stack' => 'Stack 0',
                    'data' => $dataSomme[1],
                ],
                [   'label' => 'Correct',
                    'backgroundColor' => 'yellow',
                    'stack' => 'Stack 0',
                    'data' => $dataSomme[2],
                ],
                [   'label' => 'Bien',
                    'backgroundColor' => 'green',
                    'stack' => 'Stack 0',
                    'data' => $dataSomme[3],
                ],
                [   'label' => 'Très bien',
                    'backgroundColor' => 'orange',
                    'stack' => 'Stack 0',
                    'data' => $dataSomme[4],
                ],
            ],
        ]);

        $chart->setOptions([
            'indexAxis' =>'y',
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        $chartApport->setData([
            'labels' => ['Cette formation :'],
            'datasets' => [
                [
                    'label' => 'ne m\'a rien apporté',
                    'backgroundColor' => 'blue',
                    'stack' => 'Stack 0',
                    'data' => array($dataApport[0]),
                ],

                [
                    'label' => 'a confirmé ce que je savais déjà',
                    'backgroundColor' => 'red',
                    'stack' => 'Stack 0',
                    'data' => array($dataApport[1]),
                ],

                [
                    'label' => 'm\'a apporté de nouvelles connaissances',
                    'backgroundColor' => 'yellow',
                    'stack' => 'Stack 0',
                    'data' => array($dataApport[2]),
                ],

                [
                    'label' => 'm\'a permis d`\'échanger avec les participant.e.s',
                    'backgroundColor' => 'green',
                    'stack' => 'Stack 0',
                    'data' => array($dataApport[3]),
                ],

            ],
        ]);

        $chartApport->setOptions([
            'indexAxis' => 'y',
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        return $this->render('retour/afficheur.html.twig', [
            'chart' => $chart,
            'chartApport' => $chartApport,
            'dataApport' => $dataApport,
            'dataMoyenne' => $dataMoyenne,
            'dataRemarque' => $dataRemarque,
            'dataQuestion' => $dataQuestion,
        ]);
    }

    #[Route('/QRCodeGen/{seanceID}', name: 'qrcode')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function qrCode(EntityManagerInterface $entityManager, $seanceID)
    {
        $seance = $entityManager->getRepository(Seance::class)->findByID(strval($seanceID))[0];

        $writer = new PngWriter();
        $qrCode = QrCode::create('https://15.236.191.187/retour/new/'.$seanceID)
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(120)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $logo = Logo::create('build/images/logo.png')
        ->setResizeToWidth(60);
        $label = Label::create('retour')->setFont(new NotoSans(8));

        $qrCodes = [];;

        $qrCode->setSize(400)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
        $qrCodes['withImage'] = $writer->write(
            $qrCode,
            $logo,
            $label->setText('retour')->setFont(new NotoSans(20))
        )->getDataUri();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $dompdf->set_option('isHtml5ParserEnabled', true);

        $html = $this->renderView('retour/retourQRCodePDF.html.twig', [
            'seance' => $seance,
            'qrCodes' => $qrCodes
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("retourQRCode".$seance->getName().".pdf", [
            "Attachment" => true
        ]);
    }


    #[Route('/QRCodeEventGen/{evenementID}', name: 'qrcodeEvent')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function qrCodeEvent(EntityManagerInterface $entityManager, $evenementID)
    {   $seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
      
        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];

        $writer = new PngWriter();
        $qrCode = QrCode::create('https://15.236.191.187/retour/newEvent/' . $evenementID)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(120)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $logo = Logo::create('build/images/logo.png')
        ->setResizeToWidth(60);
        $label = Label::create('retour')->setFont(new NotoSans(8));

        $qrCodes = [];;

        $qrCode->setSize(400)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
        $qrCodes['withImage'] = $writer->write(
            $qrCode,
            $logo,
            $label->setText('retour')->setFont(new NotoSans(20))
        )->getDataUri();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $dompdf->set_option('isHtml5ParserEnabled', true);

        $html = $this->renderView('retour/retourEventQRCodePDF.html.twig', [
            'evenement' => $evenement,
            'qrCodes' => $qrCodes
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("retourQRCode" . $evenement->getName() . ".pdf", [
            "Attachment" => true
        ]);
    }
}
