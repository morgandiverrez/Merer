<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\ChartOfAccounts;
use App\Entity\Comptability\TransactionLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TransactionLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('label', TextType::class, ['required' => false, 'label' => 'Label'])
            ->add('date', DateType::class,['widget' => 'single_text', 'label' => 'date',])
            ->add('amount', MoneyType::class, ['label' => 'Montant ',])
            ->add('urlProof', FileType::class, [
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'label' => 'document justificatif',
            ])
              
            ->add('quote', TextAreaType::class, [
                    'label' => 'commentaire',
                    ])
            ->add('chartOfAccounts', EntityType::class, [
                'class' => ChartOfAccounts::class,
                 'label' => 'PCG',
                 'required' => false,
            ])

           
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TransactionLine::class,
        ]);
    }
}
