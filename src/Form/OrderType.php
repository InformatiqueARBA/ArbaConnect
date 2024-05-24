<?php

namespace App\Form;

use App\Entity\Corporation;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('deliveryDate', null, [
                'widget' => 'single_text',
                'label' => 'Date de livraison'
            ])
            ->add('type', null, [
                'disabled' => true,
                'label' => 'N° de bon',
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
            ->add('corporation', EntityType::class, [
                'class' => Corporation::class,
                'choice_label' => 'id',
                'disabled' => true,
                'label' => 'N° de bon', 'attr' => ['style' => 'display:none;'],
                'label' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'modifier',
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
