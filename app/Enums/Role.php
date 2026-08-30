<?php

namespace App\Enums;

enum Role: string
{
    case Admin        = 'admin';
    case Staff        = 'staff';
    case KepalaGudang = 'kepala_gudang';
    case Management   = 'management';

    /** Label tampilan bahasa Indonesia */
    public function label(): string
    {
        return match($this) {
            Role::Admin        => 'Admin',
            Role::Staff        => 'Staff',
            Role::KepalaGudang => 'Kepala Gudang',
            Role::Management   => 'Management',
        };
    }

    /** Semua nilai string role untuk validasi Laravel */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** String untuk rule validasi: "admin,staff,kepala_gudang,management" */
    public static function validationRule(): string
    {
        return implode(',', self::values());
    }
}
