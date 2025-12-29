<?php

// src/Form/ComplementType.php
namespace App\Form;

use App\Entity\Complement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ComplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du complément',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Coca Cola']
            ])
            ->add('type_complement', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Frite' => 'FRITE',
                    'Boisson' => 'BOISSON',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('prix', IntegerType::class, [
                'label' => 'Prix (FCFA)',
                'attr' => ['class' => 'form-control', 'placeholder' => '1000']
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                        new File(
                            maxSize: '2M',
                            mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Complement::class,
        ]);
    }
}