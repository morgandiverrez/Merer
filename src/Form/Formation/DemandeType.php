<?php

namespace App\Form\Formation;

use App\Entity\Formation\Demande;
use App\Entity\Formation\EquipeElu;
use App\Entity\Formation\Formation;
use App\Entity\Formation\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add( 'dateDebut', DateTimeType::class, [
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
        ])
            ->add('dateFin', DateTimeType::class, [
                'date_widget' => 'single_text',
              'time_widget' => 'single_text',
              'required'=>false,
            ])
            ->add('nombrePersonne')
            ->add('planning', FileType::class, [
                'required' => false,
                'mapped' => false,
                ])
            ->add('doubleMaillage')
            ->add('association', EntityType::class, [
                'class'=> Association::class,
                 'multiple' => true,
                 'required' => false,
            ])
            ->add( 'equipeElu', EntityType::class, [
            'class' => EquipeElu::class,
            'multiple' => true,
            'required' => false,
            ])
            ->add('formation', EntityType::class, [
                'class'=> Formation::class,
                'multiple'=>true,
                 'required' => false,
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}
