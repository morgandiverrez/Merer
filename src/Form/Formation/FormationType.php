<?php

namespace App\Form\Formation;

use App\Entity\Formation\Badge;
use App\Entity\Formation\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
            ->add('name')
            ->add('description', TextareaType::class)
            ->add('pre_requis')
            ->add( 'duration', TimeType::class, [
           
            'widget' => 'single_text',
        ]) 
            ->add('public_cible')
            ->add('opg', TextareaType::class)
            ->add('badge', EntityType::class, [
                'class' => Badge::class,
                'label' =>"Badge",
                'required' => false,
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
