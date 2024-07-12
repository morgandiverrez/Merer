<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\BankDetail;
use App\Form\Comptability\BankDetailType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

 #[Route('/bankDetail', name: 'bankDetail_')]
class BankDetailController extends AbstractController
{
    //pour les customers
    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_USER')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {   
         $user = $this->getUser();
        $customer = $user->getCustomer();
        if($customer->getBankDetails()[0]) {
            $bankDetail = $customer->getBankDetails()[0];
        }else{
             $bankDetail = new BankDetail();
        }
       
        $bankDetail->setCustomer($customer);
        $form = $this->createForm(BankDetailType::class, $bankDetail);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($bankDetail);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show', []);
        }

        return $this->render('Comptability/bankDetail/new.html.twig', [
            'bankDetail' => $bankDetail,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{bankDetailID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $bankDetailID): Response
    {
         
        $bankDetail = $entityManager->getRepository(BankDetail::class)->findById($bankDetailID)[0];

        $form = $this->createForm(BankDetailType::class, $bankDetail);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($bankDetail);
            $entityManager->flush();
            return $this->redirectToRoute('profil_show', []);
        }

        return $this->render('Comptability/bankDetail/new.html.twig', [
            'bankDetail' => $bankDetail,
            'form' => $form->createView(),
        ]);
    }


}
