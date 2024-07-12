<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Event;
use App\Entity\Comptability\Seance;
use App\Form\Comptability\EventType;
use App\Entity\Comptability\Exercice;
use App\Entity\Comptability\TransactionLine;
use App\Controller\Comptability\ImpressionController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/event', name: 'event_')]
class EventController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {   $seances = $entityManager->getRepository(Seance::class)->findAllSuperiorByDatetimeAndVisibleAndWithoutEvenement(date('y/m/d H:i:s'));
       
        $events = $entityManager->getRepository(Event::class)->findAllInOrder();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $events = array_intersect($events, $entityManager->getRepository(Event::class)->findAllByName($posts['name']));
            }
        }
        $totals = [];
        foreach( $events as $event){
                $totals[$event->getId()] = $entityManager->getRepository(TransactionLine::class)->totalByEvent($event)['total'];
        }
        
        return $this->render('Comptability/event/showAll.html.twig', [
            'events' => $events,
            'totals' => $totals,

        ]);
    }

    #[Route('/show/{eventID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $eventID): Response
    {
        $event = $entityManager->getRepository(Event::class)->findById($eventID)[0];
        $total = $entityManager->getRepository(TransactionLine::class)->totalByEvent($event)['total'];
        $totalTransaction = [];
        foreach($event->getTransactions() as $transaction ){
            $totalTransaction[$transaction->getId()] = $entityManager->getRepository(TransactionLine::class)->totalByTransaction($transaction->getId())['total'];
        }
        $totalImpression = [];
        foreach($event->getImpressions() as $impression ){
            $totalImpression[$impression->getId()] =(new ImpressionController)->impressionTotale( $entityManager, $impression->getId());
        }

        return $this->render('Comptability/event/show.html.twig', [
            'event' => $event,
            'total' => $total,
            'totalTransaction' => $totalTransaction,
             'totalImpression' => $totalImpression

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
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
          
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute('event_show', ['eventID' => $event->getId()]);
        }

        return $this->render('Comptability/event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),


        ]);
    }



    #[Route('/edit/{eventID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $eventID): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));

        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $event = $entityManager->getRepository(Event::class)->findById($eventID)[0];
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute('event_show', ['eventID' => $eventID]);
        }

        return $this->render('Comptability/event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{eventID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $eventID): Response
    {

        $event = $entityManager->getRepository(Event::class)->findById($eventID)[0];
        $entityManager->remove($event);
        $entityManager->flush();

        return $this->redirectToRoute('event_showAll');
    }

 
}
