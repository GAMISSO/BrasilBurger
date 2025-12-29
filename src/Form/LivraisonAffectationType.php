<?php

// src/Form/LivraisonAffectationType.php
namespace App\Form;

use App\Entity\Livreur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivraisonAffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('livreur_id', EntityType::class, [
                'class' => Livreur::class,
                'choice_label' => 'nom',
                'label' => 'Sélectionner un livreur',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Choisissez un livreur',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('l')
                        ->where('l.disponibilite = :dispo')
                        ->setParameter('dispo', 'Disponible')
                        ->orderBy('l.nom', 'ASC');
                },
            ])
            ->add('commandes', ChoiceType::class, [
                'label' => 'Commandes à affecter',
                'choices' => $options['commandes'],
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'form-check-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'commandes' => [],
        ]);
    }
}