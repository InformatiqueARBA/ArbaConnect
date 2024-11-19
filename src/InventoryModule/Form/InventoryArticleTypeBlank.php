<?php

namespace App\InventoryModule\Form;

use App\Entity\Security\InventoryArticle;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryArticleTypeBlank extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // Vérifiez le rôle de l'utilisateur
        $isEditable = $this->security->isGranted('ROLE_ADMIN');

        $builder
            ->add('inventoryNumber', null, [
                'disabled' => !$isEditable,
                'attr' => ['style' => 'font-size:14px; text-align :center;'],
                'label' => false
            ])
            ->add('warehouse', null, [
                'disabled' => !$isEditable,
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
                'disabled' => !$isEditable,
                'label' => false
            ])
            ->add('designation2', null, [
                'disabled' => !$isEditable,
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
            ->add('preparationUnit', null, [
                'disabled' => !$isEditable,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('packagingName', null, [
                'disabled' => !$isEditable,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            ->add('typeArticle', null, [
                'disabled' => !$isEditable,
                'attr' => ['style' => 'font-size:14px;text-align :center;'],
                'label' => false
            ])
            // ->add('divisible', ChoiceType::class, [
            //     'choices' => [
            //         'Oui' => true,
            //         'Non' => false,
            //     ],
            //     'required' => true,
            // ]);
            ->add('divisible', null, [
                'disabled' => !$isEditable,
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
