<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\BP;
use App\Form\Comptability\BPType;
use App\Entity\Comptability\Exercice;
use App\Entity\Comptability\TransactionLine;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Comptability\ChartOfAccounts;


#[Route('/bp', name: 'bp_')]
class BPController extends AbstractController
{

    #[Route('/', name: 'showAllBPActual')]
    #[IsGranted('ROLE_TRESO')]
    public function showAllBPActual(): Response
    {
        $date =  date("Y");
        return $this->redirectToRoute('bp_showAll', ['exercice' => $date]);

    }

    #[Route('/exercicePrecedent/{exercice}', name: 'showAllBPPrecedent')]
    #[IsGranted('ROLE_TRESO')]
    public function showAllBPPrecedent( $exercice): Response
    {
        $annee = $exercice-1;
        return $this->redirectToRoute('bp_showAll', ['exercice' => $annee]);
    }

    #[Route('/exerciceSuivant/{exercice}', name: 'showAllBPSuivant')]
    #[IsGranted('ROLE_TRESO')]
    public function showAllBPSuivant( $exercice): Response
    {
  
        $annee = $exercice + 1;
        return $this->redirectToRoute('bp_showAll', ['exercice' => $annee]);
    }


    #[Route('/exercice/{exercice}', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(ChartBuilderInterface $chartBuilder, EntityManagerInterface $entityManager , Request $request, $exercice): Response
    {
        $bpProduits = $entityManager->getRepository(BP::class)->findAllByExerciceProduit($exercice);
        $totalsProduits = [];
        foreach ($bpProduits as $produit) {
            $totalsProduits[$produit->getId()] = $entityManager->getRepository(TransactionLine::class)->totalByBP($produit)['total'];
        }

        $bpCharges = $entityManager->getRepository(BP::class)->findAllByExerciceCharge($exercice);
        $totalsCharges = [];
        foreach ($bpCharges as $charge) {
            $totalsCharges[$charge->getId()] = $entityManager->getRepository(TransactionLine::class)->totalByBP($charge)['total'];
        }

        $comptes6 = $entityManager->getRepository(ChartOfAccounts::class)->findAllByCategorie(60000);
        $comptes7 = $entityManager->getRepository(ChartOfAccounts::class)->findAllByCategorie(70000);
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);

        $datas = array(
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        );

        foreach ($comptes6 as $compte) {
           
            $datas[0][intval(date('m'))] +=  $entityManager->getRepository(ChartOfAccounts::class)->totalByChartOfAccounts($compte->getId())['total'];
        }
        foreach ($comptes7 as $compte) {
            $datas[1][intval(date('m'))] +=  $entityManager->getRepository(ChartOfAccounts::class)->totalByChartOfAccounts($compte->getId())['total'];
        }

        $chart->setData([
            'labels' => ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],          
              'datasets' => [
                [
                    'label' => 'Dépense',
                    'backgroundColor' => 'red',
                    'stack' => 'Stack 0',
                    'data' => $datas[0],
                ],
                [
                    'label' => 'Recette',
                    'backgroundColor' => 'green',
                    'stack' => 'Stack 0',
                    'data' => $datas[1],
                ],

            ],
        ]);

        $chart->setOptions([
            'indexAxis' => 'x',
    "responsive"=>true,
    "plugins"=>[
      "legend"=>[
        "position"=>'top',
      ],
      "title"=>[
        "display"=>true,
        "text"=>''
      ]
    ]

            
        ]);

        return $this->render('Comptability/bp/showAll.html.twig', [
            'bpProduits' => $bpProduits,
            'bpCharges' => $bpCharges,
            'totalsProduits' => $totalsProduits,
            'totalsCharges' => $totalsCharges,
            'exercice' => $exercice,
            'chart' => $chart,
        ]);
    }

    #[Route('/show/{bpID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show( EntityManagerInterface $entityManager, $bpID): Response
    {
        $bp = $entityManager->getRepository(BP::class)->findById($bpID)[0];
        $total = $entityManager->getRepository(TransactionLine::class)->totalByBP($bp)['total'];

        $totalsTransactions = [];
        foreach ($bp->getTransactions() as $transaction) {
            $totalsTransactions[$transaction->getId()] = $entityManager->getRepository(TransactionLine::class)->totalByTransaction($transaction->getId())['total'];
        }
        return $this->render('Comptability/bp/show.html.twig', [
            'bp' => $bp,
            'totalsTransactions' => $totalsTransactions,
            'total' => $total,

        ]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));

        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $bp = new BP();
        $form = $this->createForm(BPType::class, $bp);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bp);
            $entityManager->flush();
            return $this->redirectToRoute('bp_show', ['bpID' => $bp->getId()]);
        }

        return $this->render('Comptability/bp/new.html.twig', [
            'bp' => $bp,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{bpID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $bpID): Response
    {
        $exercice = $entityManager->getRepository(Exercice::class)->findOneByAnnee(intval(date('Y')));

        if (!$exercice) {
            $exercice = new Exercice();
            $exercice->setAnnee(intval(date('Y')));
            $entityManager->persist($exercice);
            $entityManager->flush();
        }
        $bp = $entityManager->getRepository(BP::class)->findById($bpID)[0];
        $form = $this->createForm(BPType::class, $bp);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bp);
            $entityManager->flush();
            return $this->redirectToRoute('bp_show', ['bpID' => $bpID]);
        }

        return $this->render('Comptability/bp/edit.html.twig', [
            'bp' => $bp,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{bpID}', name: 'delete')]
    #[IsGranted('ROLE_TRESO')]
    public function delete(EntityManagerInterface $entityManager, $bpID): Response
    {

        $bp = $entityManager->getRepository(BP::class)->findById($bpID)[0];
        $entityManager->remove($bp);
        $entityManager->flush();

        return $this->redirectToRoute('bp_showAllBPActual');
    }

    
}
