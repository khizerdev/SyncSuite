<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
            'father_name' => 'required|string|max:255',
            'passport_number' => 'required|string|max:255',
            'reporting_manager' => 'required|string|max:255',
            'employement_status' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'cnic_number' => 'required|string|max:20',
            'email' => 'required|email',
            'dob' => 'required|date',
            'code' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'shift_id' => 'required|exists:shifts,id',
            'type_id' => 'required|exists:employee_types,id',
            'branch_id' => 'required|exists:branches,id',
            'hiring_date' => 'required|date',
            'salary' => 'required|numeric',
        ];
    }
}
