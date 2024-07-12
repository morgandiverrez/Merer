<?php

namespace App\Form\Formation;

use App\Entity\Formation\Retour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RetourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note_contenu', ChoiceType::class,[
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    
                ]
            ])

            ->add('remarque_contenu')
            ->add('note_animation', ChoiceType::class,[
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,

                ]
            ])
            ->add('remarque_animation')
            ->add('note_implication', ChoiceType::class, [
            'choices' => [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,

            ]
        ])
            ->add('remarque_implication')
            ->add('note_reponse_atente', ChoiceType::class, [
            'choices' => [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,

            ]
        ])
            ->add('remarque_reponse_attente')
            ->add('note_niv_competence', ChoiceType::class, [
            'choices' => [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,

            ]
        ])
            ->add('remarque_niv_competence')
            ->add('note_utilite', ChoiceType::class, [
            'choices' => [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,

            ]
        ])
            ->add('remarque_utilite')
            ->add('note_generale', ChoiceType::class, [
            'choices' => [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,

            ]
        ])
            ->add('remarque_generale')
            ->add('apport_generale', ChoiceType::class,[
                'choices' => [
                    'ne m\'a rien apporté' => 'ne m\'a rien apporté',
                    'a confirmé ce que je savais déjà' => 'a confirmé ce que je savais déjà',
                    'm\'a apporté de nouvelles connaissances' => 'm\'a apporté de nouvelles connaissances',
                    'm\'a permis d\'échanger avec les participant.e.s' => 'm\'a permis d\'échanger avec les participant.e.s',
            ],
            ])
            ->add('plus_aimer')
            ->add('moins_aimer')
            ->add('aimer_voir')
            ->add('mot_fin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Retour::class,
        ]);
    }
}
