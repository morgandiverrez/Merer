<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\FundBox;
use App\Entity\Comptability\Location;
use App\Entity\Comptability\ChartOfAccounts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FundBoxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextAreaType::class)
            ->add('onecent', NumberType::class, ['required'=>false,])
            ->add('twocent', NumberType::class, ['required' => false,])
            ->add('fivecent', NumberType::class, ['required' => false,])
            ->add('tencent', NumberType::class, ['required' => false,])
            ->add('twentycent', NumberType::class, ['required' => false,])
            ->add('fiftycent', NumberType::class, ['required' => false,])
            ->add('oneeuro', NumberType::class, ['required' => false,])
            ->add('twoeuro', NumberType::class, ['required' => false,])
            ->add('fiveeuro', NumberType::class, ['required' => false,])
            ->add('teneuro', NumberType::class, ['required' => false,])
            ->add('twentyeuro', NumberType::class, ['required' => false,])
            ->add('fiftyeuro', NumberType::class, ['required' => false,])
            ->add('hundredeuro', NumberType::class, ['required' => false,])
            ->add('twohundredeuro', NumberType::class, ['required' => false,])
            ->add('fivehundredeuro', NumberType::class, ['required' => false,])
       
            ->add('location', EntityType::class, [
            'class' => Location::class,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
