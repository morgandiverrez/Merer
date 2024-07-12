<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Customer;
use App\Entity\Comptability\Supplier;
use App\Entity\Comptability\ChartOfAccounts;
use App\Form\Comptability\CustomerType;
use App\Form\Comptability\SupplierType;
use App\Controller\Comptability\InvoiceController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

 #[Route('/supplier', name: 'supplier_')]
class SupplierController extends AbstractController
{
   #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $suppliers = $entityManager->getRepository(Supplier::class)->findAll();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $suppliers = array_intersect($suppliers, $entityManager->getRepository(Supplier::class)->findAllByName($posts['name']));
            }
           
        }
        return $this->render('Comptability/supplier/showAll.html.twig', [
            'suppliers' => $suppliers,

        ]);
    }

    #[Route('/show/{supplierID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $supplierID): Response
    {

        $supplier = $entityManager->getRepository(Supplier::class)->findById($supplierID)[0];
        
        
        
        return $this->render('Comptability/supplier/show.html.twig', [
            'supplier' => $supplier,

        ]);
    }


    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $chartOfAccount = new ChartOfAccounts;
            $chartOfAccount->setName('supplier_'.$form->get('name')->getData());
            $chartOfAccount->setMovable(true);
           if (isset($entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(4000000)[0])) {
                $nbChartOfAccount = $entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(4000000)[0]['code'];
                $chartOfAccount->setCode($nbChartOfAccount + 1);
            } else {
                $chartOfAccount->setCode(4000000);
            }
            $supplier->setChartOfAccounts($chartOfAccount);
            $entityManager->persist($chartOfAccount);
            $entityManager->persist($supplier);
            $entityManager->flush();
            return $this->redirectToRoute('supplier_show', ['supplierID' => $supplier->getId()]);
        }

        return $this->render('Comptability/supplier/new.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),


        ]);
    }

    #[Route('/edit/{supplierID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $supplierID): Response
    {
        $supplier = $entityManager->getRepository(Supplier::class)->findById($supplierID)[0];
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
           
            $entityManager->persist($supplier);
            $entityManager->flush();
            return $this->redirectToRoute('supplier_show', ['supplierID' => $supplierID]);
        }

        return $this->render('Comptability/supplier/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form->createView(),

        ]);
    }
}
