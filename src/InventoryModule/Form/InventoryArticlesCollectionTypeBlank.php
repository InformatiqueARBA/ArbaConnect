<?php

namespace App\InventoryModule\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\InventoryModule\Form\InventoryArticleTypeBlank;
use Symfony\Bundle\SecurityBundle\Security;

class InventoryArticlesCollectionTypeBlank extends AbstractType


{


    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // Vérifiez le rôle de l'utilisateur
        $admin = $this->security->isGranted('ROLE_ADMIN');
        // Définir dynamiquement le label en fonction d'une condition
        $label = $admin ? 'Enregistrer les articles' : 'Enregistrer l\' article';

        $builder
            ->add('articles', CollectionType::class, [
                'entry_type' => InventoryArticleTypeBlank::class,
                'entry_options' => ['label' => false],
                'allow_add' => false, // on ne veut pas ajouter des articles dynamiquement
                'label' => false,
            ])

            ->add('save', SubmitType::class, [
                'label' => $label,
            ]);
    }
}
