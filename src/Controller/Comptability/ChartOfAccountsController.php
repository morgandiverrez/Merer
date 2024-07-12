<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\EquipeElu;
use App\Entity\Comptability\ChartOfAccounts;
use App\Form\Comptability\ChartOfAccountsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/chartOfAccounts', name: 'chartOfAccounts_')]
class ChartOfAccountsController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $accounts = $entityManager->getRepository(ChartOfAccounts::class)->findAllInOrder();
        if ($request->isMethod('post')) {
            $posts = $request->request->all();
            if ($posts['code']) {
                $accounts = array_intersect($accounts, $entityManager->getRepository(ChartOfAccounts::class)->findAllByCode($posts['code']));
            }
        }
        
        return $this->render('Comptability/chart_of_accounts/showAll.html.twig', [
            'accounts' => $accounts,
        ]);
    }

    #[Route('/show/{accountID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $accountID): Response
    {
        $account = $entityManager->getRepository(ChartOfAccounts::class)->findById($accountID)[0];

        return $this->render('chart_of_accounts/show.html.twig', [
            'account' => $account,

        ]);
    }

    #[Route('/edit/{accountID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $accountID): Response
    {
        $account = $entityManager->getRepository(ChartOfAccounts::class)->findById($accountID)[0];
        $form = $this->createForm(ChartOfAccountsType::class, $account);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($account);
            $entityManager->flush();
            return $this->redirectToRoute('chartOfAccounts_showAll',[]);
        }

        return $this->render('chart_of_accounts/edit.html.twig', [
            'account' => $account,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{accountID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $accountID): Response
    {

        $account = $entityManager->getRepository(ChartOfAccounts::class)->findById($accountID)[0];
        $entityManager->remove($account);
        $entityManager->flush();

        return $this->redirectToRoute('chartOfAccounts_showAll');
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $chartOfAccounts = new ChartOfAccounts();
        $form = $this->createForm(chartOfAccountsType::class, $chartOfAccounts);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chartOfAccounts);
            $entityManager->flush();
            return $this->redirectToRoute('chartOfAccounts_showAll');
        }

        return $this->render('chart_of_accounts/new.html.twig', [
            'chartOfAccounts' => $chartOfAccounts,
            'form' => $form->createView(),
        ]);
    }

//     #[Route('/insert/{code}/{name}/{movable}', name: 'insert')]
//     #[IsGranted('ROLE_BF')]
//     public function insert(EntityManagerInterface $entityManager, $code, $name, $movable): Response
//     {
//         $chartOfAccounts = new ChartOfAccounts();
//         $chartOfAccounts->setCode($code);
//         $chartOfAccounts->setName($name);
//         $chartOfAccounts->setMovable($movable);
//         $entityManager->persist($chartOfAccounts);
//         $entityManager->flush();

//         return $this->redirectToRoute('customer_insert', ['locID' =>  $this->get("session")->get("location"), 'accountID' => $chartOfAccounts->getId()]);
//     }
 }
