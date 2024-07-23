<?php

namespace App\Http\Requests\ProductType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {

        return [
            'name' => 'required|string|max:255',
            'material_id' => 'required|exists:materials,id', // Assuming material is selected from a dropdown linked to a materials table
            'particular_id' => 'required|exists:particulars,id', // Assuming particular is selected from a dropdown linked to a particulars table
        ];
    }
}