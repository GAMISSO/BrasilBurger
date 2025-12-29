<?php
// src/Form/BurgerType.php
namespace App\Form;

use App\Entity\Burger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BurgerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du burger',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Brasil Burger Classic']
            ])
            ->add('prix', IntegerType::class, [
                'label' => 'Prix (FCFA)',
                'attr' => ['class' => 'form-control', 'placeholder' => '5000']
            ])
            ->add('image_url', FileType::class, [
                'label' => 'Image du burger',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                        new File(
                            maxSize: '2M',
                            mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                            mimeTypesMessage: 'Veuillez uploader une image valide (JPEG, PNG, WEBP)',
                        )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Burger::class,
        ]);
    }
}