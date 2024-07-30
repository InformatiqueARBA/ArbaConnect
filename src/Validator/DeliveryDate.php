<?php


namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DeliveryDate extends Constraint
{
    public $message = 'La date de livraison doit être modifiée.';
}
