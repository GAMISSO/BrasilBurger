<?php
// src/Form/MenuType.php
namespace App\Form;

use App\Entity\Menu;
use App\Entity\Burger;
use App\Entity\Complement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du menu',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Menu Complet']
            ])
            ->add('burger_id', EntityType::class, [
                'class' => Burger::class,
                'choice_label' => 'nom',
                'label' => 'Burger inclus',
                'attr' => ['class' => 'form-select', 'data-price' => 'burger'],
                'placeholder' => 'Sélectionnez un burger',
                'mapped' => false,
                'required' => false
            ])
            ->add('complements', EntityType::class, [
                'class' => Complement::class,
                'choice_label' => 'nom',
                'label' => 'Complements inclus',
                'attr' => ['class' => 'form-check-input', 'id' => 'complements_select'],
                'placeholder' => 'Sélectionnez des complements',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('prix_total', IntegerType::class, [
                'label' => 'Prix total (FCFA)',
                'attr' => ['class' => 'form-control', 'placeholder' => '8000', 'id' => 'prix_total', 'readonly' => 'readonly'],
                'required' => true
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du menu',
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
            'data_class' => Menu::class,
        ]);
    }
}