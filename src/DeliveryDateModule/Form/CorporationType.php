<?php

namespace App\DeliveryDateModule\Form;

use App\Entity\Acdb\Corporation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CorporationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', null, [
                'disabled' => true,
                'label' => 'ID'
            ])
            ->add('name', null, [
                'label' => 'Name'
            ])
            ->add('status', null, [
                'label' => 'Status'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Corporation::class,
        ]);
    }
}
