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
            'department_id' => 'required|exists:departments,id', // Assuming department is selected from a dropdown linked to a departments table
            'code' => 'required|string|max:255',
            'manufacturer_id' => 'required|exists:manufacturers,id', // Assuming manufacturer is selected from a dropdown linked to a manufacturers table
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:255|',
            'purchased_date' => 'required|date_format:Y-m-d', // Assuming date format is Y-m-d
            'model_date' => 'required|date_format:Y-m-d', // Assuming date format is Y-m-d
            'capacity' => 'required|integer|min:1', // Assuming capacity is an integer
            'production_speed' => 'required|numeric|min:0', // Assuming speed is a numeric value
            'price' => 'required|numeric|min:0', // Assuming price is a numeric value
            'warranty' => 'required', // Warranty expiry should be a valid date after purchase date
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Adjust mime types and max size as needed 
            'remarks' => 'nullable|string|max:1000', // Assuming notes/remarks are optional and have a longer text limit
        ];        
    }
}
