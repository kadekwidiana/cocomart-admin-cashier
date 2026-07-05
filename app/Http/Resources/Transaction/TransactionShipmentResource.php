<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'grabDeliveryId' => $this->grab_delivery_id,
            'grabShippingCost' => $this->grab_shipping_cost,
            'receiverName' => $this->receiver_name,
            'receiverPhoneNumber' => $this->receiver_phone_number,
            'receiverAddress' => $this->receiver_address,
            'receiverLatitude' => $this->receiver_latitude,
            'receiverLongitude' => $this->receiver_longitude,
            'status' => [
                'value' => $this->status?->value,
                'label' => $this->status?->label(),
            ],
            'grabJsonResponse' => $this->grab_json_response,
        ];
    }
}
