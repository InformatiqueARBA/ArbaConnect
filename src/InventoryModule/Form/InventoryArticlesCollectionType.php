<?php

namespace App\InventoryModule\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\InventoryModule\Form\InventoryArticleType;

class InventoryArticlesCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('articles', CollectionType::class, [
                'entry_type' => InventoryArticleType::class,
                'entry_options' => ['label' => false],
                'allow_add' => false, // on ne veut pas ajouter des articles dynamiquement
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer tous les articles'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Pas de classe associée directement
        ]);
    }
}
