<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state_order', ChoiceType::class, [
                'label' => 'État',
                'property_path' => 'stateOrder',
                'required' => false,
                'placeholder' => 'Tous les états',
                'choices' => [
                    'En attente' => 'EN_ATTENTE',
                    'En cours de validation' => 'EN_COURS_VALIDATION',
                    'Validée' => 'VALIDEE',
                    'En préparation' => 'EN_PREPARATION',
                    'Prête' => 'PRETE',
                    'En livraison' => 'EN_LIVRAISON',
                    'Terminée' => 'TERMINEE',
                    'Annulée' => 'ANNULEE',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('type_livraison', ChoiceType::class, [
                'label' => 'Type de livraison',
                'required' => false,
                'placeholder' => 'Tous les types',
                'choices' => [
                    'Sur place' => 'Sur_place',
                    'À retirer' => 'A_retirer',
                    'À livrer' => 'A_livrer',
                ],
                'property_path' => 'typeLivraison',
                'attr' => ['class' => 'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}