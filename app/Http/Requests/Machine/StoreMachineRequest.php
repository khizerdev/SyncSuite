<?php

namespace App\Http\Requests\Machine;


use Illuminate\Foundation\Http\FormRequest;

class StoreMachineRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'department_id' => 'nullable|exists:departments,id',
            'code' => 'required|string|max:255',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'name' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:255',
            'purchased_date' => 'nullable|date_format:Y-m-d',
            'model_date' => 'nullable|date_format:Y-m-d',
            'capacity' => 'nullable|integer|min:1',
            'production_speed' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'warranty' => 'nullable',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'remarks' => 'nullable|string|max:1000',
        ];        
    }

}
