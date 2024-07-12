<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\Location;
use App\Entity\Comptability\Supplier;
use App\Entity\Comptability\ChartOfAccounts;
use App\Entity\Comptability\AdministrativeIdentifier;
use Symfony\Component\Form\AbstractType;
use App\Form\Comptability\AdministrativeIdentifierType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SupplierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('location', EntityType::class, [
            'class' => Location::class,
        ])
           ->add('administrativeIdentifier', EntityType::class, [
            'class' => AdministrativeIdentifier::class,
        ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
        ]);
    }
}
