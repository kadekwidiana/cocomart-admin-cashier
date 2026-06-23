<?php

namespace App\Services\External\Grab;

use App\Models\Transaction;
use App\Services\External\Oxy\LocationOxyService;

class GrabDeliveryPayloadBuilder
{
    public static function build(
        Transaction $transaction,
        string $oxyAccessToken,
        string $vehicleType,
        ?string $serviceType = null
    ): array {
        $shipment = $transaction->shipment;

        $location = self::resolveStoreLocation($oxyAccessToken, $transaction->oxy_location_id);

        $origin = [
            'address' => $location['addressStreet'] ?? ($location['address'] ?? '-'),
            'coordinates' => [
                'latitude' => (float) ($location['latitude'] ?? $location['lat'] ?? 0),
                'longitude' => (float) ($location['longitude'] ?? $location['lng'] ?? 0),
            ],
        ];

        $destination = [
            'address' => $shipment->receiver_address,
            'coordinates' => [
                'latitude' => (float) $shipment->receiver_latitude,
                'longitude' => (float) $shipment->receiver_longitude,
            ],
        ];

        $packages = [];
        foreach ($transaction->items as $item) {
            $packages[] = [
                'name' => 'Item',
                'description' => '',
                'quantity' => (int) $item->quantity,
                'price' => (int) $item->price,
                'dimensions' => ['height' => 0, 'width' => 0, 'depth' => 0, 'weight' => 0],
            ];
        }

        return [
            'serviceType' => $serviceType ?? config('services.grab.default_service_type'),
            'vehicleType' => $vehicleType,
            'codType' => 'REGULAR',
            'packages' => $packages,
            'origin' => $origin,
            'destination' => $destination,
        ];
    }

    public static function withDeliveryDetails(
        array $quotePayload,
        Transaction $transaction,
        string $oxyAccessToken
    ): array {
        $shipment = $transaction->shipment;

        $location = self::resolveStoreLocation($oxyAccessToken, $transaction->oxy_location_id);

        return array_merge($quotePayload, [
            'merchantOrderID' => $transaction->id,
            'paymentMethod' => 'CASHLESS',
            'highValue' => false,
            'recipient' => [
                'firstName' => $shipment->receiver_name,
                'phone' => $shipment->receiver_phone_number,
                'smsEnabled' => true,
            ],
            'sender' => [
                'firstName' => $location['name'] ?? 'Store',
                'companyName' => $location['name'] ?? 'Store',
                'phone' => $location['phone'] ?? $shipment->receiver_phone_number,
                'smsEnabled' => true,
            ],
        ]);
    }

    public static function resolveStoreLocation(string $oxyAccessToken, string $oxyLocationId): array
    {
        try {
            $res = LocationOxyService::getLocations(token: $oxyAccessToken);
            $locations = $res['data']['data'] ?? [];

            foreach ($locations as $location) {
                if ((string) ($location['id'] ?? '') === (string) $oxyLocationId) {
                    return $location;
                }
            }

            return $locations[0] ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
