<?php

namespace App\Enums;

enum TransactionShipmentStatus: string
{
    case PENDING = 'PENDING';           // Request ke Grab dibuat
    case DRIVER_ASSIGNED = 'DRIVER_ASSIGNED';
    case PICKED_UP = 'PICKED_UP';
    case ON_THE_WAY = 'ON_THE_WAY';
    case DELIVERED = 'DELIVERED';
    case CANCELED = 'CANCELED';
    case FAILED = 'FAILED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Driver',
            self::DRIVER_ASSIGNED => 'Driver Ditugaskan',
            self::PICKED_UP => 'Barang Diambil',
            self::ON_THE_WAY => 'Dalam Pengiriman',
            self::DELIVERED => 'Terkirim',
            self::CANCELED => 'Dibatalkan',
            self::FAILED => 'Gagal Dikirim',
        };
    }
}
