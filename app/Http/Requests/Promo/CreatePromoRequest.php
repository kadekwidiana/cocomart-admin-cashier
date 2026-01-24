<?php

namespace App\Http\Requests\Promo;

use Illuminate\Foundation\Http\FormRequest;

class CreatePromoRequest extends FormRequest
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
            'title' => 'required|string',
            'code' => 'required|string|unique:promos,code',
            'discount_percentage' => 'required|numeric',
            'is_active' => 'required|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
