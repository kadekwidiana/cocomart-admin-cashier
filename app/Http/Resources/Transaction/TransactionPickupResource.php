<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionPickupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pickupCode' => $this->pickup_code,
            'pickupTime' => $this->pickup_time,
            'pickupEndTime' => $this->pickup_end_time,
            'receiverName' => $this->receiver_name,
            'receiverPhoneNumber' => $this->receiver_phone_number,
            'status' => [
                'value' => $this->status?->value,
                'label' => $this->status?->label(),
            ],
        ];
    }
}
