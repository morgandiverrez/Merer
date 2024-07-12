<?php

namespace App\Form\Comptability;

use App\Entity\Comptability\BP;
use App\Entity\Comptability\Event;
use App\Entity\Comptability\Exercice;
use App\Entity\Comptability\FinancementLine;
use App\Entity\Comptability\Transaction;
use App\Form\Comptability\TransactionLineType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('exercice', EntityType::class,[
                'class' => Exercice::class,
             'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                ->orderBy('u.annee', 'DESC');
            },
            ])
            ->add('closure', ChoiceType::class, [
                'choices'  => [
                    'Non' => false,
                    'Oui' => true,
                   
                ],
            ])
            ->add('quote', TextAreaType::class)
            ->add('transactionLines', CollectionType::class,[
                    'entry_type' => TransactionLineType::class,
                    "label" => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]
            )
            ->add('event',  EntityType::class, [
            'class' => Event::class,
            ])
            ->add('bp',  EntityType::class, [
            'class' => BP::class,
            ])
            ->add('financementLine',  EntityType::class, [
            'class' => FinancementLine::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
