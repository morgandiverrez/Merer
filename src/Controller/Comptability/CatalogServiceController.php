<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\CatalogService;
use App\Form\Comptability\CatalogServiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/service', name: 'service_')]
class CatalogServiceController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $services = $entityManager->getRepository(CatalogService::class)->findAll();
        
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $services = array_intersect($services, $entityManager->getRepository(CatalogService::class)->findAllByName($posts['name']));
            }
           
          
        }
        return $this->render('Comptability/catalog_service/showAll.html.twig', [
            'services' => $services,

        ]);
    }

    #[Route('/show/{serviceID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager,   $serviceID): Response
    {
        $service = $entityManager->getRepository(CatalogService::class)->findById($serviceID);

        return $this->render('Comptability/catalog_service/show.html.twig', [
            'service' => $service,

        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $service = new CatalogService();
        $form = $this->createForm(CatalogServiceType::class, $service);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $service->setAmountTtc($service->getAmountHt() * (1 + ($service->getTvaRate() / 100)));
            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('service_show', ['serviceID' => $service->getId()]);
        }

        return $this->render('Comptability/catalog_service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),


        ]);
    }



    #[Route('/edit/{serviceID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $serviceID): Response
    {
        $service = $entityManager->getRepository(CatalogService::class)->findById($serviceID);
        $form = $this->createForm(CatalogServiceType::class, $service);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $service->setAmountTtc($service->getAmountHt() * (1 + ($service->getTvaRate() / 100)));
            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('service_show', ['serviceID' => $serviceID]);
        }

        return $this->render('Comptability/catalog_service/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{serviceID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $serviceID): Response
    {

        $service = $entityManager->getRepository(CatalogService::class)->findById($serviceID);
        $entityManager->remove($service);
        $entityManager->flush();

        return $this->redirectToRoute('service_showAll');
    }
}
