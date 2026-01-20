<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'PENDING';               // Transaksi dibuat, belum dibayar
    case PAID = 'PAID';                     // Pembayaran berhasil
    case CONFIRMED = 'CONFIRMED';           // Admin / sistem mengkonfirmasi
    case IN_PROCESS = 'IN_PROCESS';         // Sedang diproses
    case COMPLETED = 'COMPLETED';           // Transaksi selesai
    case CANCELED = 'CANCELED';             // Dibatalkan

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Sudah Dibayar',
            self::CONFIRMED => 'Dikonfirmasi',
            self::IN_PROCESS => 'Diproses',
            self::COMPLETED => 'Selesai',
            self::CANCELED => 'Dibatalkan',
        };
    }
}
