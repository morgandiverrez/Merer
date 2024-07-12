<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\BankAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accountNumber')
            ->add('ribBankCode')
            ->add('ribBranchCode')
            ->add('ribAccountNumber')
            ->add('ribKey')
            ->add('iban')
            ->add('bic')
            ->add('lastCountDate')
            ->add('location')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BankAccount::class,
        ]);
    }
}
