<?php

namespace App\Form\Formation;

use App\Entity\Formation\EquipeElu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EquipeEluType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('categorie')
            ->add('description', TextareaType::class)
            ->add('adresse_mail', EmailType::class)
            ->add('etablissement')
            ->add('fede_filliere')
            ->add('date_election', BirthdayType::class, [
                'widget' => 'single_text',
            ])
            ->add('duree_mandat');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EquipeElu::class,
        ]);
    }
}
