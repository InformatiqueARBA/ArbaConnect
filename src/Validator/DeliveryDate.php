<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class DeliveryDate extends Constraint
{
    public string $message = 'La date de livraison doit être modifiée.';

    public function __construct(
        string $message = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($groups, $payload);
        $this->message = $message ?? $this->message;
    }
}
