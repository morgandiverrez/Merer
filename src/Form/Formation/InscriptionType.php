<?php

namespace App\Form\Formation;

use App\Entity\Formation\Lieux;
use App\Entity\Formation\SeanceProfil;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('attente')
            ->add('lieu', EntityType::class, [
                'class' => Lieux::class,
                'choices' => $options['liste_lieu'],
         
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeanceProfil::class,
            'liste_lieu' => false,
        ]);

        $resolver->setAllowedTypes('liste_lieu', 'Doctrine\ORM\PersistentCollection');
    }
}
