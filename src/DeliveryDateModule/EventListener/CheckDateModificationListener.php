<?php

namespace App\DeliveryDateModule\EventListener;

use DateTime;
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
        $this->dateValidator($event);
    }

    public function dateValidator(FormEvent $event)
    {

        $form = $event->getForm();
        $data = $event->getData(); // Valeur de l'utilisateur
        $originalDeliveryDate = $form->getData()->getDeliveryDate(); // Valeur pré setter
        $originalOrderDate = $form->getData()->getOrderDate(); // Valeur pré setter

        // Comparer les 2 dates pour obliger le changement de date si validation
        if ($data['deliveryDate'] === $originalDeliveryDate->format('d/m/Y')) {
            $form->addError(new FormError('Err_Saisie'));
        }
        // Conversion date de commande en DateTime + 90jrs pour délai max
        $deliveryDate = DateTime::createFromFormat('d/m/Y', $data['deliveryDate']);
        $originalOrderDate->modify('+90 days');

        // Comparaison date saisie avec délai max
        if ($deliveryDate > $originalOrderDate) {
            $form->addError(new FormError('Err_90jours'));
        }
    }
}
