<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $numericRules = ['nullable', 'numeric', 'min:0'];

        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'period' => ['required', 'string', 'max:50'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'target_work_days' => $numericRules,
            'work_days' => $numericRules,
            'overtime_hours' => $numericRules,
            'special_overtime_hours' => $numericRules,
            'position_allowance' => $numericRules,
            'attendance_allowance' => $numericRules,
            'safety_incentive' => $numericRules,
            'risk_allowance' => $numericRules,
            'placement_allowance' => $numericRules,
            'golden_shake_hand' => $numericRules,
            'tax_allowance' => $numericRules,
            'irregular_income' => $numericRules,
            'overtime_pay' => $numericRules,
            'total_income' => $numericRules,
            'bpjamsostek' => $numericRules,
            'bpjs_health' => $numericRules,
            'attendance_deduction' => $numericRules,
            'fine' => $numericRules,
            'employee_receivable' => $numericRules,
            'pph21_tax_object' => $numericRules,
            'total_deduction' => $numericRules,
            'take_home_pay' => $numericRules,
            'allowance' => $numericRules,
            'bonus' => $numericRules,
            'overtime' => $numericRules,
            'deduction' => $numericRules,
            'send_email' => ['sometimes', 'boolean'],
        ];
    }
}
