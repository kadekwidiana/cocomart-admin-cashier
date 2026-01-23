<?php

namespace App\Http\Requests\ImageSlider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImageSliderRequest extends FormRequest
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
            'image' => 'sometimes|required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'sometimes|required|string',
            'index' => 'sometimes|required|integer|unique:image_sliders,index,' . $this->id,
            'is_active' => 'sometimes|required|boolean',
        ];
    }
}
