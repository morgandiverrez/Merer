<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Cheque;
use App\Form\Comptability\ChequeType;
use App\Entity\Comptability\ChequeBox;
use App\Form\Comptability\ChequeBoxType;
use App\Entity\Comptability\ChartOfAccounts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/chequeBox', name: 'chequeBox_')]
class ChequeBoxController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $chequeBoxs = $entityManager->getRepository(ChequeBox::class)->findAll();
        $totals=[];
        foreach($chequeBoxs as $box  ){
            $totals[$box->getName()] = $entityManager->getRepository(ChequeBox::class)->montantTotale($box->getId())[0]['total_amount']; 
            if($totals[$box->getName()] == null)$totals[$box->getName()]=0;
        }

        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $chequeBoxs = array_intersect($chequeBoxs, $entityManager->getRepository(ChequeBox::class)->findAllByName($posts['name']));
            }
        }
        return $this->render('Comptability/chequeBox/showAll.html.twig', [
            'chequeBox' => $chequeBoxs,
            'totals'=>$totals,

        ]);
    }

    #[Route('/show/{chequeBoxID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $chequeBoxID): Response
    {

        $chequeBox = $entityManager->getRepository(ChequeBox::class)->findById($chequeBoxID)[0];

        $total = 0;
        $total= $entityManager->getRepository(ChequeBox::class)->montantTotale($chequeBox->getId())[0]['total_amount'];
        
        return $this->render('Comptability/chequeBox/show.html.twig', [
            'chequeBox' => $chequeBox,
            'total'=> $total,

        ]);
    }


    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $chequeBox = new ChequeBox();
        $form = $this->createForm(ChequeBoxType::class, $chequeBox);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $chartOfAccount = new ChartOfAccounts;
            $chartOfAccount->setName('chequeBox_'.$form->get('name')->getData());
            $chartOfAccount->setMovable(true);
           if (isset($entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(5120000)[0])) {
                $nbChartOfAccount = $entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(5120000)[0]['code'];
                $chartOfAccount->setCode($nbChartOfAccount + 1);
            } else {
                $chartOfAccount->setCode(51201);
            }
            $chequeBox->setChartOfAccounts($chartOfAccount);
            $entityManager->persist($chartOfAccount);

            foreach ($chequeBox->getCheques() as $cheque) {
                $entityManager->persist($cheque);
            }
            $entityManager->persist($chequeBox);
            $entityManager->flush();
            return $this->redirectToRoute('chequeBox_show', ['chequeBoxID' => $chequeBox->getId()]);
        }

        return $this->render('Comptability/chequeBox/new.html.twig', [
            'chequeBox' => $chequeBox,
            'form' => $form->createView(),


        ]);
    }

    #[Route('/edit/{chequeBoxID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $chequeBoxID): Response
    {
        $chequeBox = $entityManager->getRepository(ChequeBox::class)->findById($chequeBoxID)[0];
        $form = $this->createForm(ChequeBoxType::class, $chequeBox);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($chequeBox->getCheques() as $cheque) {
                $entityManager->persist($cheque);
            }
            $entityManager->persist($chequeBox);
            $entityManager->flush();
            return $this->redirectToRoute('chequeBox_show', ['chequeBoxID' => $chequeBoxID]);
        }

        return $this->render('Comptability/chequeBox/edit.html.twig', [
            'chequeBox' => $chequeBox,
            'form' => $form->createView(),

        ]);
    }


    #[Route('/delete/{chequeBoxID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $chequeBoxID): Response
    {

        $chequeBox = $entityManager->getRepository(ChequeBox::class)->findById($chequeBoxID)[0];
        $entityManager->remove($chequeBox);
        $entityManager->flush();

        return $this->redirectToRoute('chequeBox_showAll');
    }

   
}
