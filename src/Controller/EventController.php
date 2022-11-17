<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
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
    {
        $events = $entityManager->getRepository(Event::class)->findAllInOrder();

        if ($request->isMethod('post')) {
            $posts = $request->request->all();

            
            if ($posts['name']) {
                $events = array_intersect($events, $entityManager->getRepository(Event::class)->findAllByName($posts['name']));
            }
            
        }
        return $this->render('event/showAll.html.twig', [
            'events' => $events,

        ]);
    }

    #[Route('/show/{eventID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $eventID): Response
    {
        $event = $entityManager->getRepository(Event::class)->findById($eventID)[0];

        return $this->render('event/show.html.twig', [
            'event' => $event,

        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute('event_show', ['eventID' => $event->getId()]);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),


        ]);
    }



    #[Route('/edit/{eventID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $eventID): Response
    {
        $event = $entityManager->getRepository(Event::class)->findById($eventID)[0];
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute('event_show', ['eventID' => $eventID]);
        }

        return $this->render('event/edit.html.twig', [
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