<?php


namespace App\DeliveryDateModule\Enum;

class Profil
{
    public const ROOT = 'root';
    public const ADMIN = 'admin';
    public const SUPER_USER = 'superUser';
    public const USER = 'user';

    // Méthode pour obtenir toutes les valeurs de l'enum
    public static function getAllRoles(): array
    {
        return [
            self::ROOT,
            self::ADMIN,
            self::SUPER_USER,
            self::USER,
        ];
    }
}
