<?php
// src/Enum/Status.php

namespace App\Enum;

class Status
{
    public const EDITABLE = 'editable';
    public const UNEDITABLE = 'uneditable';
    public const CANCELLED = 'cancelled';
    public const EDITED = 'edited';

    // Méthode pour obtenir toutes les valeurs de l'enum
    public static function getAllStatuses(): array
    {
        return [
            self::EDITABLE,
            self::UNEDITABLE,
            self::CANCELLED,
            self::EDITED,
        ];
    }
}
