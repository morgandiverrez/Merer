<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Location;
use App\Form\Comptability\LocationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/location', name: 'location_')]
class LocationController extends AbstractController
{
    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {

        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show');
        }

        return $this->render('Comptability/location/new.html.twig', [
            'location' => $location,
            'form' => $form->createView(),
        ]);
    }

    // #[Route('/', name: 'showAll')]
    // #[IsGranted('ROLE_BF')]
    // public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    // {
    //     $lieux = $entityManager->getRepository(Location::class)->findAll();

    //     if ($request->isMethod('post')) {
    //         $posts = $request->request->all();
    //         if ($posts['name']) {
    //             $lieux = array_intersect($lieux, $entityManager->getRepository(Lieux::class)->findAllByName($posts['name']));
    //         }
    //         if ($posts['ville']) {
    //             $lieux = array_intersect($lieux, $entityManager->getRepository(Lieux::class)->findAllByVille($posts['ville']));
    //         }
    //     }

    //     return $this->render('lieux/showAll.html.twig', [
    //         'lieux' => $lieux,

    //     ]);
    // }

    #[Route('/show/{locationID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $locationID): Response
    {

        $location = $entityManager->getRepository(Location::class)->findById($locationID)[0];

        return $this->render('Comptability/location/show.html.twig', [
            'location' => $location,

        ]);
    }

    #[Route('/edit/{locationID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $locationID): Response
    {
        $location = $entityManager->getRepository(location::class)->findById($locationID)[0];
        $form = $this->createForm(locationType::class, $location);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($location);
            $entityManager->flush();
            return $this->redirectToRoute('location_show', ['locationID' => $locationID]);
        }

        return $this->render('Comptability/location/new.html.twig', [
            'location' => $location,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{locationID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $locationID): Response
    {

        $location = $entityManager->getRepository(location::class)->findById($locationID)[0];
        $entityManager->remove($location);
        $entityManager->flush();


        return $this->redirectToRoute('account', []);
    }

}
