<?php

namespace App\ArbaConnect\Enum;

class Status
{
    public const EDITABLE = 'editable';
    public const UNEDITABLE = 'uneditable';
    public const CANCELLED = 'cancelled';
    public const EDITED = 'edited';
    public const PRINTED = 'printed';
    public const DONE = 'done';

    // Méthode pour obtenir toutes les valeurs de l'enum
    public static function getAllStatuses(): array
    {
        return [
            self::EDITABLE,
            self::UNEDITABLE,
            self::CANCELLED,
            self::EDITED,
            self::PRINTED,
            self::DONE,
        ];
    }
}
