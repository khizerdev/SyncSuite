<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorRequest extends FormRequest
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
        $customerId = $this->route('customer');

        return [
            'name' => 'required|string|max:255',
            // 'email' => [
            //     'sometimes',
            //     'required',
            //     'string',
            //     'email',
            //     'max:255',
            //     Rule::unique('customers')->ignore($customerId),
            // ],
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
            'balance_type' => 'required|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
        ];
    }
}
