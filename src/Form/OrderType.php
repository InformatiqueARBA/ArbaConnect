<?php
// OrderType.php

namespace App\Form;

use App\Entity\Acdb\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', null, [
                'disabled' => true,
                'label' => 'N° de bon'
            ])
            ->add('orderStatus', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('reference', null, [
                'disabled' => true,
                'label' => 'Référence'
            ])
            ->add('orderDate', null, [
                'widget' => 'single_text',
                'disabled' => true,
                'label' => 'Date de commande'
            ])
            ->add('deliveryDate', TextType::class, [
                'mapped' => false, // Ne pas mapper directement sur l'entité
                'label' => 'Date de livraison',
                'attr' => [
                    'class' => 'form-control custom-typography flatpickr-input',
                    'placeholder' => 'Sélectionner une date',
                    'data-date-format' => 'd-m-Y',
                ]
            ])
            ->add('type', null, [
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('seller', null, [
                'disabled' => true,
                'label' => 'Vendeur'
            ])
            ->add('comment', null, [
                'disabled' => true,
                'label' => 'Commentaire'
            ])
            ->add('corporation', CorporationType::class, [
                'label' => false,
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
