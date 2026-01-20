<?php

namespace App\Enums;

enum TransactionFulfillmentType: string
{
    case PICKUP = 'PICKUP';
    case SHIPMENT = 'SHIPMENT';

    public function label(): string
    {
        return match ($this) {
            self::PICKUP => 'Pickup',
            self::SHIPMENT => 'Shipment',
        };
    }
}
