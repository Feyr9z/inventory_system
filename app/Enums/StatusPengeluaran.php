<?php

namespace App\Enums;

enum StatusPengeluaran: string
{
    case PENDING   = 'pending';
    case DISETUJUI = 'disetujui';
    case DITOLAK   = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::PENDING   => 'Menunggu Persetujuan',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK   => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING   => 'badge-subtle-warning',
            self::DISETUJUI => 'badge-subtle-success',
            self::DITOLAK   => 'badge-subtle-danger',
        };
    }
}
