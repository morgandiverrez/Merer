<?php

namespace App\Controller\Formation;

use DateTime;
use App\Entity\Formation\Badge;
use App\Form\Formation\BadgeType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



#[Route('/badge', name: 'badge_')]

class BadgeController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function showAll(EntityManagerInterface$entityManager, Request $request): Response
    {
        $badges = $entityManager->getRepository(Badge::class)->findAll();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $badges = array_intersect($badges, $entityManager->getRepository(Badge::class)->findAllByName($posts['name']));
            }
            if ($posts['categorie']) {
                $badges = array_intersect($badges, $entityManager->getRepository(Badge::class)->findAllByCategorie($posts['categorie']));
            }
            if ($posts['description']) {
                $badges = array_intersect($badges, $entityManager->getRepository(Badge::class)->findAllByDescription($posts['description']));
            }
        }
        return $this->render('Formation_/badge/showAll.html.twig', [     
            'badges' => $badges,
            
        ]);
    }

    #[Route('/show/{badgeID}', name: 'show')]
    #[IsGranted('ROLE_FORMATEURICE')]
    public function show(EntityManagerInterface $entityManager, $badgeID): Response
    {

        $badge = $entityManager->getRepository(Badge::class)->findById($badgeID)[0];
        
        return $this->render('Formation_/badge/show.html.twig', [
            'badge'=> $badge,
            
        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_BF')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $badge = new Badge();
        $form = $this->createForm(BadgeType::class, $badge);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $badge->setDateCreation(new Datetime());
            $logoUpload = $form->get('image')->getData();
            $name = $form->get('name')->getData();
            $nameMajuscule = strtoupper($name);

            if ($logoUpload) {
                $logoFileName = 'badge_' . $nameMajuscule . '.' . $logoUpload->guessExtension();
                $badge->setImage('build/badgeFormation//'.$logoFileName);
                try {
                    $logoUpload->move(
                        'build/badge',
                        $logoFileName
                    );
                } catch (FileException $e) {
                }

            }
            $entityManager->persist($badge);
            $entityManager->flush();
            return $this->redirectToRoute('badge_showAll', []);
        }

        return $this->render('Formation_/badge/new.html.twig', [
            'badge' => $badge,
            'form' => $form->createView(),
            
        ]);
    }



    #[Route('/edit/{badgeID}', name: 'edit')]
    #[IsGranted('ROLE_BF')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $badgeID): Response
    {
        $badge = $entityManager->getRepository(Badge::class)->findById($badgeID)[0];
        $form = $this->createForm(BadgeType::class, $badge);
        $form->handleRequest($request);
       

        if ($form->isSubmitted() && $form->isValid()) {
            
            $logoUpload = $form->get('image')->getData();
            $name = $form->get('name')->getData();
            $nameMajuscule = strtoupper($name);

            if ($logoUpload) {
                $logoFileName = 'logo_' . $nameMajuscule . '.' . $logoUpload->guessExtension();
                $badge->setImage('build/badgeFormation//' . $logoFileName);
                try {
                    $logoUpload->move(
                        'files/badge',
                        $logoFileName
                    );
                } catch (FileException $e) {
                }
            }
            $entityManager->persist($badge);
            $entityManager->flush();
            return $this->redirectToRoute('badge_showAll');
        }

        return $this->render('Formation_/badge/edit.html.twig', [
            'badge' => $badge,
            'form' => $form->createView(),
            
            ]);
    }



    #[Route('/delete/{badgeID}', name: 'delete')]
    #[IsGranted('ROLE_FORMA')]
    public function delete(EntityManagerInterface $entityManager, $badgeID): Response
    {

        $badge = $entityManager->getRepository(Badge::class)->findById($badgeID)[0];
        unlink($badge->getImage());
        $entityManager->remove($badge);
        $entityManager->flush();
       
        return $this->redirectToRoute('badge_showAll', []);
    }
}
