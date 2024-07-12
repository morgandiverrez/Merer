<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\Financement;
use App\Form\Comptability\FinancementLineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FinancementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('financeur')
            ->add('amount', MoneyType::class)
            ->add('pluriannuel', NumberType::class)
            ->add('dateVersement', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateSignature', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('flechee')
            ->add(
                'financementLines',
                CollectionType::class,
                [
                    'entry_type' => FinancementLineType::class,
                    "label" => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Financement::class,
        ]);
    }
}
