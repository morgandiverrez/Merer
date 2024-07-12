<?php

namespace App\Controller\Comptability;

use App\Entity\Comptability\Contact;
use App\Entity\Comptability\Customer;
use App\Entity\Comptability\Supplier;
use App\Entity\Comptability\ChequeBox;
use App\Form\Comptability\CustomerType;
use App\Form\Comptability\ChequeBoxType;
use App\Entity\Comptability\ChartOfAccounts;
use App\Controller\Comptability\InvoiceController;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Comptability\ExpenseReportController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/customer', name: 'customer_')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_TRESO')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $customers = $entityManager->getRepository(Customer::class)->findAll();
       

        return $this->render('Comptability/customer/showAll.html.twig', [
            'customers' => $customers,

        ]);
    }

    #[Route('/show/{customerID}', name: 'show')]
    #[IsGranted('ROLE_TRESO')]
    public function show(EntityManagerInterface $entityManager, $customerID): Response
    {

        $customer = $entityManager->getRepository(Customer::class)->findById($customerID)[0];
        
        $totals = array();
        foreach ($customer->getInvoices() as $invoice) {
            array_push($totals, (new InvoiceController)->invoiceTotale($invoice));
        }

        $totalExpenseReports = array();
        if($customer->getSupplier()){
            foreach ($customer->getSupplier()->getExpenseReports() as $expenseReport) {
                array_push($totalExpenseReports, (new ExpenseReportController)->expenseReportTotale($expenseReport));
            }
        }
        
        return $this->render('Comptability/customer/show.html.twig', [
            'customer' => $customer,
            'totals' => $totals,
            'totalExpenseReports' => $totalExpenseReports,

        ]);
    }


    #[Route('/showForCustomer', name: 'showForCustomer')]
    #[IsGranted('ROLE_TRESO')]
    public function showForCustomer(EntityManagerInterface $entityManager, ): Response
    {
        $user = $this->getUser();
        $customers = $entityManager->getRepository(Customer::class)->findAll();
        $i = 0 ;
        while ( ! isset($customer) and isset($customers[$i])) {
            if ($customers[$i]->getUser() == $user) {
                $customer = $customers[$i] ;
            }
            $i++;
        }

     

        $totals = array();
        foreach ($customer->getInvoices() as $invoice) {
            array_push($totals, (new InvoiceController)->invoiceTotale($invoice));
        }

        return $this->render('Comptability/customer/show.html.twig', [
            'customer' => $customer,
            'totals' => $totals,

        ]);
    }


    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_TRESO')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            
            $chartOfAccount = new ChartOfAccounts;
            $chartOfAccount->setName('customer_'.$form->get('name')->getData());
            $chartOfAccount->setMovable(true);
           if (isset($entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(4110000)[0])) {
                $nbChartOfAccount = $entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(4110000)[0]['code'];
                $chartOfAccount->setCode($nbChartOfAccount + 1);
            } else {
                $chartOfAccount->setCode(4110000);
            }
            $customer->setChartOfAccounts($chartOfAccount);
            $entityManager->persist($chartOfAccount);

            $entityManager->persist($customer);
            $entityManager->flush();
            return $this->redirectToRoute('customer_show', ['customerID' => $customer->getId()]);
        }
        return $this->render('Comptability/customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),


        ]);
    }

    #[Route('/edit/{customerID}', name: 'edit')]
    #[IsGranted('ROLE_TRESO')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $customerID): Response
    {
        $customer = $entityManager->getRepository(Customer::class)->findById($customerID)[0];
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
           
            $entityManager->persist($customer);
            $entityManager->flush();
            return $this->redirectToRoute('customer_show', ['customerID' => $customerID]);
        }

        return $this->render('Comptability/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),

        ]);
    }

      public function newCustomerForUser(EntityManagerInterface $entityManager, $user)
    {
        $customer = new Customer();
        $customer->setName($user->getEmail());
        $contact = new Contact();
        $chartOfAccounts = new ChartOfAccounts();
        $supplier = new Supplier;
        $supplier->setName($user->getEmail());
        $chartOfAccounts2 = new ChartOfAccounts();
        $customer->setUser($user);
        $supplier->setCustomer($customer);
        $customer->addContact($contact);
        $supplier->addContact($contact);
        
        $contact->setMail($user->getEmail());
        if($user->getProfil()){
            $profil =  $user->getProfil();
            $customer->setName($profil->getLastName().' '.$profil->getName());
             $supplier->setName($profil->getLastName().' '.$profil->getName());
            $contact->setName($profil->getName());
            $contact->setLastName($profil->getLastName());
            $pronom = $profil->getPronom();
            switch ($pronom){
                case 'il':
                    $contact->setCivility('Homme');
                        break;
                case 'elle':
                    $contact->setCivility('Femme');
                        break;
                case 'iel':
                    $contact->setCivility('Neutre');
                        break;
            }
            $contact->setPhone($profil->getTelephone());
        }
          
        $chartOfAccounts->setName('customer_'.$customer->getName());
        $chartOfAccounts->setMovable(true);
        if (isset($entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(4110000)[0])) {
            $nbChartOfAccount = $entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(4110000)[0]['code'];
            $chartOfAccounts->setCode($nbChartOfAccount + 1);
        } else {
            $chartOfAccounts->setCode(4110001);
        }
        $customer->setChartOfAccounts($chartOfAccounts);

        $chartOfAccounts2->setName('supplier_'.$supplier->getName());
        $chartOfAccounts2->setMovable(true);
        if (isset($entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(4000000)[0])) {
            $nbChartOfAccount = $entityManager->getRepository(ChartOfAccounts::class)->findMaxChartOfAccount(4000000)[0]['code'];
            $chartOfAccounts2->setCode($nbChartOfAccount + 1);
        } else {
            $chartOfAccounts2->setCode(4000001);
        }
        $supplier->setChartOfAccounts($chartOfAccounts2);

        $entityManager->persist($user);
        $entityManager->persist($customer);
         $entityManager->persist($supplier);
          $entityManager->persist($chartOfAccounts2);
           $entityManager->persist($chartOfAccounts);
        $entityManager->persist($contact);
        $entityManager->flush();
        
        return $customer;
    }
}
