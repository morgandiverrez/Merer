<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\CatalogService;
use App\Entity\Comptability\CatalogDiscount;
use App\Form\Comptability\CatalogServiceType;
use App\Form\Comptability\CatalogDiscountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/discount', name: 'discount_')]
class CatalogDiscountController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $discounts = $entityManager->getRepository(CatalogDiscount::class)->findAll();

        if ($request->isMethod('post')) {
            $posts = $request->request->all();

            if ($posts['code']) {
                $discounts = array_intersect($discounts, $entityManager->getRepository(CatalogDiscount::class)->findAllByCode($posts['code']));
            }
            if ($posts['name']) {
                $discounts = array_intersect($discounts, $entityManager->getRepository(CatalogDiscount::class)->findAllByName($posts['name']));
            }
            if ($posts['description']) {
                $discounts = array_intersect($discounts, $entityManager->getRepository(CatalogDiscount::class)->findAllByDescription($posts['description']));
            }
        
            
        }
        return $this->render('Comptability/catalog_discount/showAll.html.twig', [
            'discounts' => $discounts,

        ]);
    }

    #[Route('/show/{discountID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $discountID): Response
    {
        $discount = $entityManager->getRepository(CatalogDiscount::class)->findById($discountID);

        return $this->render('Comptability/catalog_discount/show.html.twig', [
            'discount' => $discount,

        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $discount = new CatalogDiscount();
        $form = $this->createForm(CatalogDiscountType::class, $discount);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($discount);
            $entityManager->flush();
            return $this->redirectToRoute('discount_show', ['discountID' => $discount->getId()]);
        }

        return $this->render('Comptability/catalog_discount/new.html.twig', [
            'discount' => $discount,
            'form' => $form->createView(),


        ]);
    }



    #[Route('/edit/{discountID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $discountID): Response
    {
        $discount = $entityManager->getRepository(CatalogDiscount::class)->findById($discountID);
        $form = $this->createForm(CatalogDiscountType::class, $discount);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($discount);
            $entityManager->flush();
            return $this->redirectToRoute('discount_show', ['discountID' => $discountID]);
        }

        return $this->render('Comptability/catalog_discount/edit.html.twig', [
            'discount' => $discount,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{discountID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $discountID): Response
    {

        $discount = $entityManager->getRepository(CatalogDiscount::class)->findById($discountID);
        $entityManager->remove($discount);
        $entityManager->flush();

        return $this->redirectToRoute('discount_showAll');
    }
}
