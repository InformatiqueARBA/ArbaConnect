<?php

namespace App\Form;

use App\Entity\Corporation;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id')
            ->add('orderStatus')
            ->add('reference')
            ->add('orderDate', null, [
                'widget' => 'single_text',
            ])
            ->add('deliveryDate', null, [
                'widget' => 'single_text',
            ])
            ->add('type')
            ->add('seller')
            ->add('comment')
            ->add('corporation', EntityType::class, [
                'class' => Corporation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
