<?php

namespace App\Form\Formation;

use App\Entity\Formation\Lieux;
use App\Form\Formation\SeanceType;
use App\Entity\Formation\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UlidType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('name')
            ->add('description', TextAreaType::class)
            ->add( 'dateDebut', DateTimeType::class, [
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
        ])
            ->add( 'dateFin', DateTimeType::class, [
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
             'required' => false,
        ])
            ->add( 'dateFinInscription', DateTimeType::class, [
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
             'required' => false,
        ])
            ->add('URL')
            ->add('autorisationPhoto')
            ->add('modePaiement')
            ->add('parcours', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('covoiturage')
            ->add('nombrePlace')
            ->add('parcoursObligatoire')
            ->add('lieu', EntityType::class, [
                'class'=> Lieux::class,
                 'required' => false,
            ])

            ->add('seances',
                CollectionType::class,
                [
                    'entry_type' => SeanceType::class,
                    "label" => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'entry_options' => [
                        'parcours' => $options['parcours_event'],
                    ],
                     'required' => false,

                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
            'parcours_event' => false,
        ]);

        $resolver->setAllowedTypes('parcours_event', 'array');
    }
}
