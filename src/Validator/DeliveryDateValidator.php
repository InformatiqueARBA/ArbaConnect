<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\Acdb\Order;

class DeliveryDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\DeliveryDate */

        if (null === $value || '' === $value) {
            return;
        }

        // Récupérer l'entité Order de l'option du validateur
        $order = $this->context->getObject();

        if (!$order instanceof Order) {
            return;
        }

        // Comparer les dates de livraison
        if ($order->getDeliveryDate() == $value) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
