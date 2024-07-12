<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\CatalogService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CatalogServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class)
            ->add('amountHt', MoneyType::class)
            ->add('tvaRate', ChoiceType::class, [
            'choices' => [
                '0' => 0,
                '2,1'=>2.1,
                '5,5'=> 5.5,
                '10'=> 10,
                '20'=> 20, 
            ]
        ])
           
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CatalogService::class,
        ]);
    }
}
