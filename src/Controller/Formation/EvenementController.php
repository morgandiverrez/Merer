<?php

namespace App\Controller\Formation;

use App\Entity\User;
use App\Entity\Formation\Profil;
use App\Entity\Formation\Evenement;
use App\Entity\Formation\Seance;
use Aws\Ses\SesClient;
use App\Form\Formation\EvenementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/evenement', name: 'evenement_')]
class EvenementController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {  $seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
       
        $evenements = $entityManager->getRepository(Evenement::class)->findAllOrderByDate();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $evenements = array_intersect($evenements, $entityManager->getRepository(Evenement::class)->findAllByName($posts['name']));
            }
            if ($posts['description']) {
                $evenements = array_intersect($evenements, $entityManager->getRepository(Evenement::class)->findAllByDescription($posts['description']));
            }
        }
        return $this->render('Formation_/evenement/showAll.html.twig', [
            'evenements' => $evenements,

        ]);
    }

    #[Route('/show/{evenementID}', name: 'show')]
    #[IsGranted('ROLE_USER')]
    public function show(EntityManagerInterface $entityManager, $evenementID): Response
    {$seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
       
        $seanceByCreneauAndParcours = [];

        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
        
        $seances = $evenement->getSeances(); //on recup tt les seances qui ont un groupe qui commence par la variable groupe
    
        foreach ($seances as $seance) {
            if ($seance->getParcours() != null) {
                $seanceByCreneauAndParcours[strval($seance->getDatetime()->format("d/m/Y H:i"))][$seance->getParcours()] = $seance;
            } else { //si pas de parcours (donc formation pour tt les parcours si plusieur parcours)
                foreach ($evenement->getParcours() as $parcours) { // on itere les parcours pour les remplir tous de cette seance
                    $seanceByCreneauAndParcours[$seance->getDatetime()->format("d/m/Y H:i")][$parcours] = $seance;
                }
            }
        }

        

        return $this->render('Formation_/evenement/show.html.twig', [
            'evenement' => $evenement,
            'seanceByCreneauAndParcours'=> $seanceByCreneauAndParcours,
        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_BF')]
    public function new(EntityManagerInterface $entityManager, Request $request, SesClient $ses): Response
    {$seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
       
        $evenement = new Evenement();
        $parcours =  [];
        foreach ($evenement->getParcours() as $parcour) {
            $parcours[$parcour] = $parcour;
        }
        $form = $this->createForm(EvenementType::class, $evenement, ['parcours_event' => $parcours]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($evenement->getDateFin() == null) {
                $evenement->setDateFin($evenement->getDateDebut());
            }
            if($evenement->getDateFinInscription() == null){
                $evenement->setDateFinInscription($evenement->getDateDebut());
            }

            foreach($evenement->getSeances() as  $seance){
               $seance->setVisible($evenement->isVisible());
                $entityManager->persist($seance);
            }
            
            $parcours = $form->get('parcours')->getData();
            $evenement->setParcours(explode(',', $parcours));
            $entityManager->persist($evenement);
            $entityManager->flush();
            $evenement = $entityManager->getRepository(Evenement::class)->findByName($form->get('name')->getData())[0];
            foreach ($form->get('seances') as $seance) {
                foreach($seance->get('profil')->getData() as $formateurice ){

                    $nameLastNameFormateurice = $formateurice;
                    $namelastname = explode(" ", $nameLastNameFormateurice);
                    $profil = $entityManager->getRepository(Profil::class)->findByName($namelastname[0], $namelastname[1])[0];
                    $seance = $entityManager->getRepository(Seance::class)->findByEvenementAndParcourAndDatetime(
                                                                                        $evenement,
                                                                                        $seance->get('name')->getData(),
                                                                                        $seance->get('datetime')->getData()
                                                                                        )[0];
                    $profil->addSeance($seance);
                    $entityManager->persist($profil);

                    $sender_email ='no-reply@*****.net';
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
                                    'Data' =>$this->renderView('emails/formateur_alloue_event.html.twig',["seance" => $seance, 'evenement' => $evenement])
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
            
            return $this->redirectToRoute('evenement_show', ['evenementID' => $evenement->getId()]);
        }

        return $this->render('Formation_/evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),

        ]);
    }



    #[Route('/edit/{evenementID}', name: 'edit')]
    #[IsGranted('ROLE_BF')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $evenementID, SesClient $ses): Response
    {$seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
       
        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
       

        $parcours = $evenement->getparcours();
        $parcours = array_combine($parcours, $parcours);
        $form = $this->createForm(EvenementType::class, $evenement, [ 'parcours_event' => $parcours]);
        $form->get('parcours')->setData(implode(",", $parcours));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            
            if($evenement->getDateFinInscription() == null){
                $evenement->setDateFinInscription($evenement->getDateDebut());
            }

            foreach($evenement->getSeances() as  $seance){
               $seance->setVisible($evenement->isVisible());
                $entityManager->persist($seance);
            }
            
            $parcours = $form->get('parcours')->getData();
            $evenement->setParcours(explode(',', $parcours));
            $entityManager->persist($evenement);
            $entityManager->flush();
            $evenement = $entityManager->getRepository(Evenement::class)->findByName($form->get('name')->getData())[0];
            foreach ($form->get('seances') as $seance) {
                foreach($seance->get('profil')->getData() as $formateurice ){

                    $nameLastNameFormateurice = $formateurice;
                    $namelastname = explode(" ", $nameLastNameFormateurice);
                    $profil = $entityManager->getRepository(Profil::class)->findByName($namelastname[0], $namelastname[1])[0];
                    $seance = $entityManager->getRepository(Seance::class)->findByEvenementAndParcourAndDatetime(
                                                                                        $evenement,
                                                                                        $seance->get('name')->getData(),
                                                                                        $seance->get('datetime')->getData()
                                                                                        )[0];
                    $profil->addSeance($seance);
                    $entityManager->persist($profil);

                    $sender_email ='no-reply@*****.net';
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
                                    'Data' =>$this->renderView('emails/formateur_alloue_event.html.twig',["seance" => $seance, 'evenement' => $evenement])
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
            
            return $this->redirectToRoute('evenement_show', ['evenementID' => $evenement->getId()]);
        }
       
            $entityManager->flush();
        return $this->render('Formation_/evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),

        ]);
    }



    #[Route('/visible/{evenementID}', name: 'cloture')]
    #[IsGranted('ROLE_FORMA')]
    public function visible(EntityManagerInterface $entityManager, $evenementID): Response
    {$seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
       
        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
        $evenement->setVisible(true);
        $entityManager->persist($evenement);
        $entityManager->flush();
        return $this->redirectToRoute('evenement_showAll');
    }

    #[Route('/unvisible/{evenementID}', name: 'uncloture')]
    #[IsGranted('ROLE_FORMA')]
    public function unvisible(EntityManagerInterface $entityManager, $evenementID): Response
    {$seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
       
        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
        $evenement->setVisible(false);
        $entityManager->persist($evenement);
        $entityManager->flush();
        return $this->redirectToRoute('evenement_showAll');
    }

    
    #[Route('/delete/{evenementID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $evenementID): Response
    {

        $evenement = $entityManager->getRepository(Evenement::class)->findById($evenementID)[0];
        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirectToRoute('evenement_showAll', []);
    }
}
