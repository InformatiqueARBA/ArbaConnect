<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CheckDateModificationListener
{

    #[AsEventListener(event: 'form.pre_submit')]
    public function onFormPreSubmit(PreSubmitEvent $event)
    {
        // Retourne la fonction si l'évènement est déclenché
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData(); // Valeur de l'utilisateur
        $originalData = $form->getData()->getDeliveryDate(); // Valeur pré setter
        // Comparer les 2 dates pour obliger le changement de date si validation
        if ($data['deliveryDate'] === $originalData->format('d/m/Y')) {
            $form->addError(new FormError('Erreur_DDL'));
        }
    }
}
