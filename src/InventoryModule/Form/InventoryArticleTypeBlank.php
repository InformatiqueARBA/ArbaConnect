<?php

namespace App\InventoryModule\Form;

use App\Entity\Security\InventoryArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryArticleTypeBlank extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('inventoryNumber', null, [
                // 'disabled' => true,
                'attr' => ['style' => 'font-size:14px; text-align :center;'],
                'label' => false
            ])
            ->add('warehouse', null, [
                // 'disabled' => true,
                'attr' => ['style' => 'font-size:14px; text-align :center;'],
                'label' => false
            ])
            ->add('location', null, [
                // 'disabled' => true,
                'attr' => ['style' => 'font-size:14px; text-align :center;'],
                'label' => false
            ])
            ->add('articleCode', null, [
                // 'disabled' => true,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('designation1', null, [
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                // 'disabled' => true,
                'label' => false
            ])
            ->add('designation2', null, [
                // 'disabled' => true,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('lotCode', null, [
                // 'disabled' => true,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('quantityLocation1', null, [
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('preparationUnit', ChoiceType::class, [
                'choices' => [
                    'UN' => 'UN',
                    'Rouleau' => 'RL',
                    'M3' => 'M3',
                ],
                'required' => true,
            ])
            ->add('packagingName', ChoiceType::class, [
                'choices' => [
                    'UN' => 'UN',
                    'M3' => 'M3',
                    'M2' => 'M2',
                ],
                'required' => true,
            ])
            ->add('typeArticle', ChoiceType::class, [
                'choices' => [
                    'Stocké' => 'ART',
                    'Variable' => 'LOV',
                ],
                'required' => true,
            ])
            // ->add('divisible', ChoiceType::class, [
            //     'choices' => [
            //         'Oui' => true,
            //         'Non' => false,
            //     ],
            //     'required' => true,
            // ]);
            ->add('divisible', null, [
                // 'disabled' => true,
                'attr' => ['style' => 'font-size:14px; text-align :center;'],
                'label' => false

            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryArticle::class,
        ]);
    }
}
