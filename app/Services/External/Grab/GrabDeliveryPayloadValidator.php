<?php

namespace App\Services\External\Grab;

use Illuminate\Support\Facades\Validator;

class GrabDeliveryPayloadValidator
{
    /**
     * Aturan payload Grab Express (Create Delivery) berdasarkan dokumentasi resmi.
     * Payload wajib lolos validator ini sebelum dikirim ke Grab.
     *
     * @return array<int,string> daftar pesan error; kosong berarti lolos.
     */
    public static function validate(array $payload): array
    {
        $rules = [
            'merchantOrderID' => ['required', 'string'],
            'serviceType'     => ['required', 'string'],
            'vehicleType'     => ['nullable', 'string'],

            'packages'                     => ['required', 'array', 'min:1'],
            'packages.*.name'              => ['required', 'string', 'max:500'],
            'packages.*.description'       => ['present', 'string', 'max:500'],
            'packages.*.quantity'          => ['required', 'integer', 'min:1'],
            'packages.*.dimensions'        => ['required', 'array'],
            'packages.*.dimensions.height' => ['required', 'integer', 'min:0'],
            'packages.*.dimensions.width'  => ['required', 'integer', 'min:0'],
            'packages.*.dimensions.depth'  => ['required', 'integer', 'min:0'],
            'packages.*.dimensions.weight' => ['required', 'integer', 'min:0'],

            // Koordinat wajib >= 6 desimal (regex pada representasi numerik)
            'origin.address'                    => ['required', 'string'],
            'origin.coordinates.latitude'       => ['required', 'regex:/^-?\d{1,3}\.\d{6,}$/'],
            'origin.coordinates.longitude'      => ['required', 'regex:/^-?\d{1,3}\.\d{6,}$/'],
            'destination.address'               => ['required', 'string'],
            'destination.coordinates.latitude'  => ['required', 'regex:/^-?\d{1,3}\.\d{6,}$/'],
            'destination.coordinates.longitude' => ['required', 'regex:/^-?\d{1,3}\.\d{6,}$/'],

            // Telepon wajib E.164 tanpa '+'/'0' di depan
            'recipient.firstName' => ['required', 'string'],
            'recipient.phone'     => ['required', 'regex:/^[1-9]\d{7,14}$/'],
            'sender.firstName'    => ['required', 'string'],
            'sender.phone'        => ['required', 'regex:/^[1-9]\d{7,14}$/'],
        ];

        $messages = [
            'origin.coordinates.latitude.regex'       => 'Latitude origin harus memiliki minimal 6 angka desimal.',
            'origin.coordinates.longitude.regex'      => 'Longitude origin harus memiliki minimal 6 angka desimal.',
            'destination.coordinates.latitude.regex'  => 'Latitude destination harus memiliki minimal 6 angka desimal.',
            'destination.coordinates.longitude.regex' => 'Longitude destination harus memiliki minimal 6 angka desimal.',
            'recipient.phone.regex' => 'Nomor penerima harus format E.164 (kode negara, tanpa 0/+).',
            'sender.phone.regex'    => 'Nomor pengirim harus format E.164 (kode negara, tanpa 0/+).',
        ];

        return Validator::make($payload, $rules, $messages)->errors()->all();
    }
}
