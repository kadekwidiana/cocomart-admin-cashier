<?php

namespace App\Http\Requests\API\Transaction;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class CreateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'oxyCustomerId' => 'required|string',
            'oxyLocationId' => 'required|string',
            'fulfillmentType' => 'required|in:SHIPMENT,PICKUP',

            'receiverName' => 'nullable|string|max:100',
            'receiverPhoneNumber' => 'nullable|string|max:20',

            'pickupTime' => 'nullable|date',

            'shipmentAddress' => 'required_if:fulfillmentType,SHIPMENT|string',
            'shipmentLatitude' => 'required_if:fulfillmentType,SHIPMENT|numeric',
            'shipmentLongitude' => 'required_if:fulfillmentType,SHIPMENT|numeric',

            'items' => 'required|array|min:1',
            'items.*.oxyItemMasterId' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
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
