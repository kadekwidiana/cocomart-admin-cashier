<?php

namespace App\Services\External\Grab;

use App\Models\Transaction;
use App\Services\External\Oxy\LocationOxyService;

class GrabDeliveryPayloadBuilder
{
    private const COUNTRY_CODE = '62'; // Indonesia

    private const DUMMY_SENDER_ADDRESS = 'Jl. Raya Uluwatu No.1, Pecatu, Kec. Kuta Sel., Kabupaten Badung, Bali 80361';
    private const DUMMY_SENDER_PHONE = '6281234567890';

    public static function build(
        Transaction $transaction,
        string $oxyAccessToken,
        string $vehicleType,
        ?string $serviceType = null
    ): array {
        $shipment = $transaction->shipment;

        $location = self::resolveStoreLocation($oxyAccessToken, $transaction->oxy_location_id);

        $coords = self::storeCoordinates($transaction->oxy_location_id);

        $origin = [
            'address' => self::DUMMY_SENDER_ADDRESS,
            'coordinates' => [
                'latitude' => (float) ($coords['latitude'] ?? 0),
                'longitude' => (float) ($coords['longitude'] ?? 0),
            ],
        ];

        if (!empty($coords['cityCode'])) {
            $origin['cityCode'] = $coords['cityCode'];
        }

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
                'phone' => self::normalizePhone($shipment->receiver_phone_number),
                'smsEnabled' => true,
            ],
            'sender' => [
                'firstName' => $location['name'] ?? 'Store',
                'companyName' => $location['name'] ?? 'Store',
                'phone' => self::DUMMY_SENDER_PHONE,
                'smsEnabled' => true,
            ],
        ]);
    }

    private static function storeCoordinates(string $oxyLocationId): array
    {
        return [
            'latitude'  => -8.655867823660703,
            'longitude' => 115.16910389011883,
            'cityCode'  => null,
        ];
    }

    private static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '0')) {
            $digits = self::COUNTRY_CODE . ltrim($digits, '0');
        }

        return $digits;
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
