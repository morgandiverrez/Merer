<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\Location;
use App\Form\Comptability\ChequeType;
use App\Entity\Comptability\ChequeBox;
use App\Entity\Comptability\ChartOfAccounts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ChequeBoxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class)
            ->add('lastCountDate',DateType::class, [
                'widget' => 'single_text',
            ])
        ->add('location', EntityType::class, [
            'class' => Location::class,
        ])
        ->add('cheques', CollectionType::class,[
                'entry_type' => ChequeType::class,
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
            'data_class' => ChequeBox::class,
        ]);
    }
}
