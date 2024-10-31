<?php

namespace App\InventoryModule\Form;

use App\Entity\Security\InventoryArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('inventoryNumber', null, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'font-size:14px;'],
            //     'label' => false
            // ])
            // ->add('warehouse', null, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'display:none; font-size:14px;'],
            //     'label' => false
            // ])
            ->add('location', null, [
                'disabled' => true,
                'attr' => ['style' => 'font-size:14px; text-align :center;'],
                'label' => false
            ])
            // ->add('location2', null, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'font-size:14px;text-align :center;'],
            //     'label' => false
            // ])
            // ->add('location3', null, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'font-size:14px;text-align :center;'],
            //     'label' => false
            // ])
            ->add('articleCode', null, [
                'disabled' => true,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('designation1', null, [
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'disabled' => true,
                'label' => false
            ])
            ->add('designation2', null, [
                'disabled' => true,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('lotCode', null, [
                'disabled' => true,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            // ->add('dimensionType', null, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'font-size:14px;'],
            //     'label' => false
            // ])
            // ->add('packaging', null, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'font-size:14px;text-align :center;'],
            //     'label' => false
            // ])

            ->add('quantityLocation1', null, [
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('quantityLocation2', null, [
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('quantityLocation3', null, [
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('packagingName', null, [
                'disabled' => true,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('preparationUnit', null, [
                'disabled' => true,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])

            // // TODO: voir pour recomptage
            // ->add('quantity2Location1', null, [
            //     'attr' => ['style' => 'display:none; font-size:14px;'],
            //     'label' => false
            // ])
            // ->add('quantity2Location2', null, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'display:none; font-size:14px;'],
            //     'label' => false
            // ])
            // ->add('quantity2Location3', null, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'display:none; font-size:14px;'],
            //     'label' => false
            // ])
            // ->add('dimensionType', HiddenType::class, [
            //     'disabled' => true,
            //     'attr' => ['style' => 'font-size:14px;'],
            //     'label' => false
            // ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryArticle::class,
        ]);
    }
}
