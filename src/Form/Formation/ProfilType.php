<?php

namespace App\Form\Formation;

use App\Entity\Formation\Profil;
use App\Entity\Formation\EquipeElu;
use App\Entity\Formation\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('last_name')
            ->add('pronom', ChoiceType::class,[
               'choices' => [
                    'il' => 'il',
                    'elle' => 'elle',
                    'iel' => 'iel'
               ],
            ])
            ->add('telephone', TelType::class)    
            ->add('date_of_birth', BirthdayType:: class, [
            'widget' => 'single_text',
        ])
            ->add('equipeElu', EntityType::class,[
                 // looks for choices from this entity
                 'class' => EquipeElu::class,
                 // uses the EquipeElu.name property as the visible option string
                
                 // used to render a select box, check boxes or radios
                 'multiple' => true,
                 'mapped' => true,
                 'required'=>false,
                
            ])

            ->add('association', EntityType::class, [
                 'class' => Association::class,
                 'mapped'=> true,
                 'multiple' => true,
            'required' => false,
                 
                
            ])
          
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profil::class,
        ]);
    }
}
