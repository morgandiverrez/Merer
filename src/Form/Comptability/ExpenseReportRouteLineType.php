<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\RepayGrid;
use App\Entity\ExpenseReportRouteLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ExpenseReportRouteLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date',  DateType::class, ['widget' => 'single_text',] )
            ->add('repayGrid', EntityType::class,[
                'class' => RepayGrid::class,
                'label' => 'selectionner un trajet',
                'required' => false,
            ])
            // ->add('start')
            // ->add('end')
            // ->add('distance')
            // ->add( 'travelMean', ChoiceType::class, [
            // 'choices' => [
            //     'voiture' => 'voiture',
            //     'bus' =>  'bus',
            //     'train' =>  'train',
            // ],
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExpenseReportRouteLine::class,
        ]);
    }
}
