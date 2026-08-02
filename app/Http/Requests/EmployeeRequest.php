<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'nip')->ignore($employee?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee?->id),
                Rule::unique('users', 'email')->ignore($employee?->user?->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'join_date' => ['required', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowance' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'create_user' => ['nullable', 'boolean'],
            'password' => [
                Rule::requiredIf(fn () => $this->boolean('create_user') && ! $employee?->user),
                'nullable',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }
}
