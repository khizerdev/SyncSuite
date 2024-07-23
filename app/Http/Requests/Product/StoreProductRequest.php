<?php

namespace App\Http\Requests\Product;


use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id|integer',
            'product_type_id' => 'required|exists:product_types,id|integer',
            'material_id' => 'required|exists:materials,id|integer',
            'particular_id' => 'required|exists:particulars,id|integer',
            'qty' => 'required|integer',
            'inventory_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'min_qty_limit' => 'required|string|max:255',
        ];
    }
}
