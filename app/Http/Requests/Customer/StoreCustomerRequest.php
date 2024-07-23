<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:customers',
            'address' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'res' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            's_man' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'strn' => 'nullable|string|max:255',
            'ntn' => 'nullable|string|max:255',
            'date' => 'required|date',
            'balance_type' => 'required|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
        ];
    }
}
