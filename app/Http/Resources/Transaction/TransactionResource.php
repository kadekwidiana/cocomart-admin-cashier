<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'oxyCustomerId' => $this->oxy_customer_id,
            'oxyLocationId' => $this->oxy_location_id,

            'status' => [
                'value' => $this->status?->value,
                'label' => $this->status?->label(),
            ],

            'fulfillmentType' => [
                'value' => $this->fulfillment_type?->value,
                'label' => $this->fulfillment_type?->label(),
            ],

            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'shippingCost' => $this->shipping_cost,
            'total' => $this->total,

            'items' => TransactionItemResource::collection(
                $this->whenLoaded('items')
            ),

            'pickup' => new TransactionPickupResource(
                $this->whenLoaded('pickup')
            ),

            'shipment' => new TransactionShipmentResource(
                $this->whenLoaded('shipment')
            ),

            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
