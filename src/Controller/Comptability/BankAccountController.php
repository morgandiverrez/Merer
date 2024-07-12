<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Supplier;
use App\Form\Comptability\SupplierType;
use App\Entity\Comptability\BankAccount;
use App\Entity\Comptability\ChartOfAccounts;
use App\Form\Comptability\BankAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bankAccount', name: 'bankAccount_')]
class BankAccountController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $bankAccounts = $entityManager->getRepository(BankAccount::class)->findAll();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['name']) {
                $bankAccounts = array_intersect($bankAccounts, $entityManager->getRepository(BankAccount::class)->findAllByName($posts['name']));
            }
        }
        return $this->render('Comptability/bankAccount/showAll.html.twig', [
            'bankAccounts' => $bankAccounts,

        ]);
    }

    #[Route('/show/{bankAccountID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $bankAccountID): Response
    {

        $bankAccount = $entityManager->getRepository(BankAccount::class)->findById($bankAccountID)[0];



        return $this->render('Comptability/bankAccount/show.html.twig', [
            'bankAccount' => $bankAccount,

        ]);
    }


    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $bankAccount = new BankAccount();
        $form = $this->createForm(BankAccountType::class, $bankAccount);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            
            $chartOfAccount = new ChartOfAccounts;
            $chartOfAccount->setName('bankAccount_'.strval($form->get('accountNumber')->getData()));
            $chartOfAccount->setMovable(true);
           if (isset($entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(5310000)[0])) {
                $nbChartOfAccount = $entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(5310000)[0]['code'];
                $chartOfAccount->setCode($nbChartOfAccount + 1);
            } else {
                $chartOfAccount->setCode(53101);
            }
            $bankAccount->setChartOfAccounts($chartOfAccount);
            $entityManager->persist($chartOfAccount);

            $entityManager->persist($bankAccount);
            $entityManager->flush();
            return $this->redirectToRoute('bankAccount_show', ['bankAccountID' => $bankAccount->getId()]);
        }

        return $this->render('Comptability/bankAccount/new.html.twig', [
            'bankAccount' => $bankAccount,
            'form' => $form->createView(),


        ]);
    }

    #[Route('/edit/{bankAccountID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $bankAccountID): Response
    {
        $bankAccount = $entityManager->getRepository(BankAccount::class)->findById($bankAccountID)[0];
        $form = $this->createForm(BankAccountType::class, $bankAccount);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($bankAccount);
            $entityManager->flush();
            return $this->redirectToRoute('bankAccount_show', ['bankAccountID' => $bankAccountID]);
        }

        return $this->render('Comptability/bankAccount/edit.html.twig', [
            'bankAccount' => $bankAccount,
            'form' => $form->createView(),

        ]);
    }


    #[Route('/delete/{bankAccountID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $bankAccountID): Response
    {

        $bankAccount = $entityManager->getRepository(BankAccount::class)->findById($bankAccountID)[0];
        $entityManager->remove($bankAccount);
        $entityManager->flush();

        return $this->redirectToRoute('bankAccount_show', ['bankAccountID' => $bankAccount->getId()]);
    }
}
