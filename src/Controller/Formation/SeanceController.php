<?php

namespace App\Controller\Formation;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use App\Entity\Formation\Lieux;
use App\Entity\Formation\Profil;
use App\Entity\Formation\Retour;
use App\Entity\Formation\Seance;
use App\Entity\Formation\Evenement;
use App\Entity\Formation\Formation;
use App\Entity\Formation\SeanceProfil;
use App\Form\Formation\SeanceSoloType;
use App\Form\Formation\SeanceType;
use Aws\Ses\SesClient;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/seance', name: 'seance_')]

class SeanceController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_USER')]
    public function showAll(EntityManagerInterface $entityManager): Response
    {

        
        $seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
      
        $evenements = $entityManager->getRepository(Evenement::class)->findAllSuperiorByDatetimeAndVisible(date('y/m/d H:i:s'));
        
    return $this->render('Formation_/seance/showAll.html.twig', [
            'evenements' => $evenements,
             'seances' => $seances,

        ]);
    }



    
    #[Route('/archivage', name: 'archivage')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function archivage(EntityManagerInterface $entityManager, Request $request): Response
    {
        $seances = $entityManager->getRepository(Seance::class)->findAllOrderByDate();

        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $seances = array_intersect($seances, $entityManager->getRepository(Seance::class)->findAllByName($posts['name']));
            }
           
            if ($posts['formation']) {
                $seances = array_intersect($seances, $entityManager->getRepository(Formation::class)->findAllByName($posts['formation']));
            }
            if ($posts['nom_lieu']) {
                $lieux = $entityManager->getRepository(Lieux::class)->findAllByName($posts['nom_lieu']);
                $lieuNameSeance = array();
                foreach ($lieux as $lieu) {
                    foreach ($lieu->getSeance() as $seance) {
                        array_push($lieuNameSeance, $seance);
                    }
                }
                $seances = array_intersect($seances, $lieuNameSeance);
            }
            if ($posts['ville']) {
                $lieux = $entityManager->getRepository(Lieux::class)->findAllByVille($posts['ville']);
                $lieuSeance = array();
                foreach ($lieux as $lieu) {
                    foreach ($lieu->getSeance() as $seance) {
                        array_push($lieuSeance, $seance);
                    }
                }
                $seances = array_intersect($seances, $lieuSeance);
            }
            if ($posts['datedebut']) {
                $seances = array_intersect($seances, $entityManager->getRepository(Seance::class)->findAllByDateTimeSuperior($posts['datedebut']));
            }
            if ($posts['datefin']) {
                $seances = array_intersect($seances, $entityManager->getRepository(Seance::class)->findAllByDateTimeInferior($posts['datefin']));
            }      
        }

        return $this->render('Formation_/seance/archivage.html.twig', [
            'seances' => $seances,
        ]);
    }


    #[Route('/showForFormateurice/{seanceID}', name: 'showForFormateurice')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function showForFormateurice(EntityManagerInterface $entityManager, $seanceID): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findByID($seanceID)[0];

        return $this->render('Formation_/seance/showForFormateurice.html.twig', [
            'seance' => $seance,
        ]);
    }

    #[Route('/show/{seanceID}', name: 'show')]
    #[IsGranted('ROLE_USER')]
    public function show(EntityManagerInterface $entityManager, $seanceID): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findByID($seanceID)[0];
        $inscrits1 = $entityManager->getRepository(SeanceProfil::class)->findAllBySeance($seance);
        $inscrits = [];
        $retours1 = $entityManager->getRepository(Retour::class)->findAllBySeance($seance);
        $retours = [];
        $dateActuelle = new DateTime();
        $i=0;
        foreach($inscrits1 as $inscrit){
            $inscrits[$i]= $inscrit['id'];
            $i++;
        }

        $i = 0;
        foreach ($retours1 as $retour) {
            $retours[$i] = $retour['id'];
            $i++;
        }
        return $this->render('Formation_/seance/show.html.twig', [
            'seance' => $seance,
            'inscrits' => $inscrits,
            'dateActuelle' => $dateActuelle,
            'retours' => $retours,
       ]);
    }

    #[Route('/liste_inscrit/{seanceID}', name: 'liste_inscrit')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function listeInscrit(EntityManagerInterface $entityManager, $seanceID): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findByID($seanceID)[0];

        return $this->render('Formation_/seance/listeInscrit.html.twig', [
            'seance' => $seance,
        ]);
    }

    #[Route('/liste_inscrit/pdf/{seanceID}', name: 'liste_inscrit_pdf')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function inscriptionPDF(EntityManagerInterface $entityManager, $seanceID)
    {

        $seance = $entityManager->getRepository(Seance::class)->findByID($seanceID)[0];

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $dompdf->set_option('isHtml5ParserEnabled', true);

        $html = $this->renderView('Formation_/seance/listeInscritPDF.html.twig', [
            'title' => "Welcome to our PDF Test",
            'seance' => $seance,
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("inscription_seance_".$seance->getName().$seance->getDatetime()->format("d/m/Y H:i").".pdf", [
            "Attachment" => true
        ]);
    }
 #[Route('/editForEvent/{seanceID}', name: 'editForEvent')]
    #[IsGranted('ROLE_BF')]
   
    public function editForEvent(EntityManagerInterface $entityManager, Request $request, SesClient $ses, $seanceID): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findById($seanceID)[0];
        $evenement = $entityManager->getRepository(Evenement::class)->findById($seance->getEvenement())[0];

        $parcours =  [];
        foreach ($evenement->getParcours() as $parcour) {
            $parcours[$parcour] = $parcour;
        }
        $form = $this->createForm(SeanceType::class, $seance,  ['parcours' => $parcours]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('profil')->getData() as  $formateurice) {
                $nameLastNameFormateurice = $formateurice;
                $namelastname = explode(" ", $nameLastNameFormateurice);
                $profil = $entityManager->getRepository(Profil::class)->findByName($namelastname[0], $namelastname[1])[0];
                $profil->addSeance($seance);
                $entityManager->persist($profil);

                $sender_email = 'no-reply@*****.net';
                $recipient_emails = [$profil->getUser()->getEmail()];

                $subject = 'Merer - Formation alloué';
                $plaintext_body = 'Formation alloué';
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
                                'Data' => $this->renderView('emails/formateur_alloue.html.twig', ["seance" => $seance])
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
            }

            foreach ($form->get('lieux')->getData() as  $lieu) {
                $nameLieux = $lieu;
                $lieux = $entityManager->getRepository(Lieux::class)->findByName(strval($nameLieux))[0];
                $lieux->addSeance($seance);
                $entityManager->persist($lieux);
            }

            if ($form->get('formation')->getData()) {
                $nameFormation = $form->get('formation')->getData();
                $formation = $entityManager->getRepository(Formation::class)->findByName(strval($nameFormation))[0];
                $formation->addSeance($seance);
                $entityManager->persist($formation);
            }

            $entityManager->persist($seance);
            $entityManager->flush();
            return $this->redirectToRoute('seance_showForFormateurice', ['seanceID' => $seance->getID()]);
        }

        return $this->render('Formation_/seance/editForEvent.html.twig', [
            'seance' => $seance,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{seanceID}', name: 'edit')]
    #[IsGranted('ROLE_BF')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $seanceID, SesClient $ses): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findById($seanceID)[0];
        $form = $this->createForm(SeanceSoloType::class, $seance);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
           
            foreach($form->get('profil')->getData() as  $formateurice){       
                $nameLastNameProfil = $formateurice;
                list($nameProfil, $lastNameProfil) = explode(" ", $nameLastNameProfil);
                $profil = $entityManager->getRepository(Profil::class)->findByName(strval($nameProfil),strval($lastNameProfil))[0];
                $profil->addSeance($seance);
                $entityManager->persist($profil);

                $sender_email = 'no-reply@*****.net';
                $recipient_emails = [$profil->getUser()->getEmail()];

                $subject = 'Merer - Formation alloué';
                $plaintext_body = 'Formation alloué' ;
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
                                'Data' =>$this->renderView('emails/formateur_alloue.html.twig', ["seance" => $seance])
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
            }

            foreach ($form->get('lieux')->getData() as  $lieu) {
                    $nameLieux = $lieu;
                    $lieux = $entityManager->getRepository(Lieux::class)->findByName(strval($nameLieux))[0];
                    $lieux->addSeance($seance);
                    $entityManager->persist($lieux);
            }

            if ($form->get('formation')->getData()) {
                $nameFormation = $form->get('formation')->getData();
                $formation = $entityManager->getRepository(Formation::class)->findByName(strval($nameFormation))[0];
                $formation->addSeance($seance);
                $entityManager->persist($formation);
            }

            $entityManager->persist($seance);
            $entityManager->flush();
            return $this->redirectToRoute('seance_showForFormateurice', ['seanceID' => $seance->getID()]);
        }

        return $this->render('Formation_/seance/edit.html.twig', [
            'seance' => $seance,
            'form' => $form->createView(),
        
        ]);
    }
   

    

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_BF')]
    public function new(EntityManagerInterface $entityManager, Request $request, SesClient $ses): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceSoloType::class, $seance);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
         
           foreach($form->get('profil')->getData() as  $formateurice){
                $nameLastNameFormateurice = $formateurice;
                $namelastname = explode(" ", $nameLastNameFormateurice);
                $profil = $entityManager->getRepository(Profil::class)->findByName($namelastname[0], $namelastname[1])[0];
                $profil->addSeance($seance);
                $entityManager->persist($profil);

                $sender_email = 'no-reply@*****.net';
                $recipient_emails = [$profil->getUser()->getEmail()];

                $subject = 'Merer - Formation alloué';
                $plaintext_body = 'Formation alloué' ;
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
                                'Data' =>$this->renderView('emails/formateur_alloue.html.twig',["seance" => $seance])
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
            }
        }
    }


    #[Route('/newForEvent/{evenementID}', name: 'newForEvent')]
    #[IsGranted('ROLE_BF')]
    public function newForEvent(EntityManagerInterface $entityManager, Request $request, SesClient $ses , $evenementID): Response
    {
        $seance = new Seance();
        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
        
         $parcours =  [];
        foreach ($evenement->getParcours() as $parcour) {
            $parcours[$parcour] = $parcour;
        }
        $form = $this->createForm(SeanceType::class, $seance,  ['parcours' => $parcours]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $seance->setEvenement($evenement);
           foreach($form->get('profil')->getData() as  $formateurice){
                $nameLastNameFormateurice = $formateurice;
                $namelastname = explode(" ", $nameLastNameFormateurice);
                $profil = $entityManager->getRepository(Profil::class)->findByName($namelastname[0], $namelastname[1])[0];
                $profil->addSeance($seance);
                $entityManager->persist($profil);

                $sender_email = 'no-reply@*****.net';
                $recipient_emails = [$profil->getUser()->getEmail()];

                $subject = 'Merer - Formation alloué';
                $plaintext_body = 'Formation alloué' ;
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
                                'Data' =>$this->renderView('emails/formateur_alloue.html.twig',["seance" => $seance])
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
            }

            foreach ($form->get('lieux')->getData() as  $lieu) {
                    $nameLieux = $lieu;
                    $lieux = $entityManager->getRepository(Lieux::class)->findByName(strval($nameLieux))[0];
                    $lieux->addSeance($seance);
                    $entityManager->persist($lieux);
            }
           
            if ($form->get('formation')->getData()) {
                $nameFormation = $form->get('formation')->getData();
                $formation = $entityManager->getRepository(Formation::class)->findByName(strval($nameFormation))[0];
                $formation->addSeance($seance);
                $entityManager->persist($formation);
            } 
    
            $entityManager->persist($seance);
            $entityManager->flush();
            return $this->redirectToRoute('seance_showForFormateurice', ['seanceID' => $seance->getID()]);
        }

        return $this->render('Formation_/seance/newForEvent.html.twig', [
            'seance' => $seance,
            'form' => $form->createView(), 
        ]);
    }


    #[Route('/visible/{seanceID}', name: 'cloture')]
    #[IsGranted('ROLE_FORMA')]
    public function visible(EntityManagerInterface $entityManager, $seanceID): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findById($seanceID)[0];
        $seance->setVisible(true);
        $entityManager->persist($seance);
        $entityManager->flush();
        return $this->redirectToRoute('seance_archivage');
    }

    #[Route('/unvisible/{seanceID}', name: 'uncloture')]
    #[IsGranted('ROLE_FORMA')]
    public function unvisible(EntityManagerInterface $entityManager, $seanceID): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->findById($seanceID)[0];
        $seance->setVisible(false);
        $entityManager->persist($seance);
        $entityManager->flush();
        return $this->redirectToRoute('seance_archivage');
    }

        #[Route('/delete/{seanceID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $seanceID): Response
    {

        $seance = $entityManager->getRepository(Seance::class)->findById($seanceID)[0];
        $entityManager->remove($seance);
        $entityManager->flush();
        

        return $this->redirectToRoute('seance_showAll', []);
    }


}