<?php

namespace App\Http\Requests\API\Transaction;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class PayTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicleType' => 'required|string|in:BIKE,CAR',
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
