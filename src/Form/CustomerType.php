<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\Customer;
use App\Entity\Location;
use App\Entity\ChartOfAccounts;
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
        ])
            ->add('administrativeIdentifier', EntityType::class, [
            'class' => AdministrativeIdentifier::class,
        ])
            ->add('contacts', EntityType::class, [
            'class' => Contact::class,
            'multiple' => true,
        ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('user')
                    ->leftJoin('user.customer', 'customer')
                     ->where('  customer.id IS NULL  ');
                },
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
