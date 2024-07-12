<?php

namespace App\Form\Comptability;

use App\Entity\User;
use App\Entity\Comptability\Contact;
use App\Entity\Comptability\Customer;
use App\Entity\Comptability\Location;
use App\Entity\Comptability\ChartOfAccounts;
use Doctrine\ORM\EntityRepository;
use App\Entity\AdministrativeIdentifier;
use ContainerJ7znfVl\getVarDumper_ContextualizedCliDumper_InnerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('impressionAccess')
            ->add('location', EntityType::class, [
            'class' => Location::class,
            'required' =>false,
        ])
            ->add('administrativeIdentifier', EntityType::class, [
                'required' => false,
            'class' => AdministrativeIdentifier::class,
        ])
            ->add('contacts', EntityType::class, [
            'class' => Contact::class,
            'required' => false,
            'multiple' => true,
        ])
          

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
