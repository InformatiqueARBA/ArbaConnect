<?php

namespace App\DeliveryDateModule\Form;

use App\DeliveryDateModule\EventListener\CheckDateModificationListener;
use App\Entity\Acdb\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
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
                    // ne pas permettre la saisie manuelle
                    'readonly' => true,
                ],
                // 'constraints' => [
                //     new DeliveryDate(),
                // ],
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
                'label' => false,
                'attr' => ['style' => 'display:none;'],
            ])
            ->add('corporation', CorporationType::class, [
                'label' => false,
                'disabled' => true,
                'attr' => ['style' => 'display:none;'],
            ])
            ->add('partialDelivery', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['class' => 'toggle-checkbox'],
            ])

            ->add('nomChantier', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'disabled' => true,
            ])
            ->add('adr1Chantier', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'disabled' => true,
            ])
            ->add('adr2Chantier', TextType::class, [
                'label' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('adr3Chantier', TextType::class, [
                'label' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('cpChantier', TextType::class, [
                'label' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('vilChantier', TextType::class, [
                'label' => false,
                'required' => false,
                'disabled' => true,
            ])

            ->add('nomSiegeSocial', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'disabled' => true,
                'disabled' => true,
            ])
            ->add('adr1SiegeSocial', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'disabled' => true,
            ])
            ->add('adr2SiegeSocial', TextType::class, [
                'label' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('adr3SiegeSocial', TextType::class, [
                'label' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('cpSiegeSocial', TextType::class, [
                'label' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('vilSiegeSocial', TextType::class, [
                'label' => false,
                'required' => false,
                'disabled' => true,
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
            ])

            ->addEventListener(FormEvents::PRE_SUBMIT, [new CheckDateModificationListener(), 'onPreSubmit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
