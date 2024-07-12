<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\Event;
use App\Entity\Comptability\Exercice;
use App\Entity\Comptability\Impression;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ImpressionForBFType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('event', EntityType::class, [
                'label' => 'projet',
                'class' => Event::class,
                'choices' => $options['liste_event'],
            ])
            ->add('name')
            ->add('format', ChoiceType::class, [
                'choices' => [
                    'A3' => 'A3',
                    'A4' =>  'A4',
                    'A5' =>  'A5',
                    'plastification' =>  'plastification',
                ]
            ])
            ->add('rectoVerso')
            ->add('couleur')
            ->add('quantite');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Impression::class,
            'liste_event' => false,
        ]);
        $resolver->setAllowedTypes('liste_event', 'array');
    }
}
