<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Contact;
use App\Form\Comptability\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/contact', name: 'contact_')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $contacts = $entityManager->getRepository(Contact::class)->findAll();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $contacts = array_intersect($contacts, $entityManager->getRepository(Contact::class)->findAllByName($posts['name']));
            }
            if ($posts['lastName']) {
                $contacts = array_intersect($contacts, $entityManager->getRepository(Contact::class)->findAllByLastName($posts['lastName']));
            }
            if ($posts['civility']) {
                $contacts = array_intersect($contacts, $entityManager->getRepository(Contact::class)->findAllByCivility($posts['civility']));
            }
            if ($posts['mail']) {
                $contacts = array_intersect($contacts, $entityManager->getRepository(Contact::class)->findAllByMail($posts['mail']));
            }
          
        }
        return $this->render('Comptability/contact/showAll.html.twig', [
            'contacts' => $contacts,

        ]);
    }

    #[Route('/show/{contactID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $contactID): Response
    {
         $contact = $entityManager->getRepository(Contact::class)->findById($contactID)[0];

        return $this->render('Comptability/contact/show.html.twig', [
            'contact' => $contact,

        ]);
    }
    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_BF')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            $contact->setLastName(mb_strtoupper($contact->getLastName()));
            $contact->setName(mb_convert_case($contact->getName(), MB_CASE_TITLE, "UTF-8"));

            $entityManager->persist($contact);
            $entityManager->flush();
            return $this->redirectToRoute('contact_show', ['contactID' => $contact->getId()]);
        }

        return $this->render('Comptability/contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{contactID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $contactID): Response
    {
        $contact = $entityManager->getRepository(Contact::class)->findById($contactID)[0];
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setLastName(mb_strtoupper($contact->getLastName()));
            $contact->setName(mb_convert_case($contact->getName(), MB_CASE_TITLE, "UTF-8"));
            $entityManager->persist($contact);
            $entityManager->flush();
            return $this->redirectToRoute('contact_show', ['contactID' => $contactID]);
        }

        return $this->render('Comptability/contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{contactID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $contactID): Response
    {

        $contact = $entityManager->getRepository(Contact::class)->findById($contactID)[0];
        $entityManager->remove($contact);
        $entityManager->flush();

        return $this->redirectToRoute('contact_showAll', []);
    }
}
