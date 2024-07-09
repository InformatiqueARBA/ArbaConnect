<?php
// OrderType.php

namespace App\Form;

use App\Entity\Acdb\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            // ->add('deliveryDate', TextType::class, [
            //     // 'mapped' => false, // Ne pas mapper directement sur l'entité
            //     'label' => 'Date de livraison',
            //     'attr' => [
            //         'class' => 'form-control custom-typography flatpickr-input',
            //         'placeholder' => 'Sélectionner une date',
            //         'data-date-format' => 'd/m/Y',
            //     ]
            // ])
            // ->add('deliveryDate', DateType::class, [
            //     'widget' => 'single_text',
            //     'label' => 'Date de livraison',
            //     'format' => 'yyyy-MM-dd',
            //     'attr' => [
            //         'class' => 'form-control custom-typography flatpickr-input',
            //         'placeholder' => 'Sélectionner une date',
            //         'data-date-format' => 'd/m/Y',
            //     ]
            // ])


            // ->add('deliveryDate', DateType::class, [
            //     'widget' => 'single_text',
            //     'html5' => true,
            //     'label' => 'Date de livraison',
            //     'attr' => [
            //         'class' => 'form-control custom-typography flatpickr-input',
            //         'placeholder' => 'Sélectionner une date',
            //     ]
            // ])

            ->add('deliveryDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false, // Disable HTML5 date input
                'label' => 'Date de livraison',
                'format' => 'dd/MM/yyyy', // Format to display
                'input' => 'datetime', // Ensure it works with DateTimeInterface
                'attr' => [
                    'class' => 'form-control custom-typography flatpickr-input',
                    'placeholder' => 'Sélectionner une date',
                    'data-date-format' => 'd/m/Y',
                ]
            ])

            //ok calendrier de base
            // ->add('deliveryDate', DateType::class, [
            //     'widget' => 'single_text',
            //     // 'html5' => true,
            //     'label' => 'Date de livraison',
            //     'attr' => [
            //         'class' => 'form-control custom-typography',
            //         'placeholder' => 'Sélectionner une date',
            //     ]
            // ])
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
                'label' => false,
                'attr' => ['style' => 'display:none;'],
            ])
            ->add('corporation', CorporationType::class, [
                'label' => false,
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
