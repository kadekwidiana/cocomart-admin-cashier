<?php

namespace App\Enums;

enum TransactionPickupStatus: string
{
    case PENDING = 'PENDING';
    case READY = 'READY';                   // Siap diambil
    case PICKED_UP = 'PICKED_UP';           // Sudah diambil
    case EXPIRED = 'EXPIRED';               // Melewati waktu pickup
    case CANCELED = 'CANCELED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::READY => 'Siap Diambil',
            self::PICKED_UP => 'Sudah Diambil',
            self::EXPIRED => 'Kadaluarsa',
            self::CANCELED => 'Dibatalkan',
        };
    }
}
