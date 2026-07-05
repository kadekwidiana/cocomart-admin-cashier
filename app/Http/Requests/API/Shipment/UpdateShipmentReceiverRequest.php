<?php

namespace App\Http\Requests\API\Shipment;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class UpdateShipmentReceiverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipmentAddress' => 'required|string',
            'shipmentLatitude' => 'required|numeric',
            'shipmentLongitude' => 'required|numeric',
            'receiverPhoneNumber' => 'required|string|max:20',
            'receiverName' => 'nullable|string|max:100',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error(
                data: $validator->errors(),
                message: 'Validation failed',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }
}
