<?php

namespace App\InventoryModule\Form;

use App\Entity\Security\InventoryArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('inventoryNumber', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('warehouse')
            ->add('location')
            ->add('location2', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('location3', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('articleCode')
            ->add('designation1')
            ->add('designation2')
            ->add('lotCode')
            ->add('dimensionType', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('packaging', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('packagingName', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('quantityLocation1')
            ->add('quantityLocation2', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('quantityLocation3', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('preparationUnit')
            ->add('quantity2Location1')
            ->add('quantity2Location2', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('quantity2Location3', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InventoryArticle::class,
        ]);
    }
}
