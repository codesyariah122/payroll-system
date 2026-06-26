<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'period',
        'target_work_days',
        'position_name',
        'department_name',
        'work_days',
        'overtime_hours',
        'special_overtime_hours',
        'basic_salary',
        'position_allowance',
        'attendance_allowance',
        'safety_incentive',
        'risk_allowance',
        'placement_allowance',
        'golden_shake_hand',
        'tax_allowance',
        'irregular_income',
        'overtime_pay',
        'total_income',
        'bpjamsostek',
        'bpjs_health',
        'attendance_deduction',
        'fine',
        'employee_receivable',
        'pph21_tax_object',
        'total_deduction',
        'take_home_pay',
        'allowance',
        'bonus',
        'overtime',
        'deduction',
        'total_salary',
        'pdf_path',
    ];

    protected $casts = [
        'target_work_days' => 'decimal:2',
        'work_days' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'special_overtime_hours' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'attendance_allowance' => 'decimal:2',
        'safety_incentive' => 'decimal:2',
        'risk_allowance' => 'decimal:2',
        'placement_allowance' => 'decimal:2',
        'golden_shake_hand' => 'decimal:2',
        'tax_allowance' => 'decimal:2',
        'irregular_income' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'total_income' => 'decimal:2',
        'bpjamsostek' => 'decimal:2',
        'bpjs_health' => 'decimal:2',
        'attendance_deduction' => 'decimal:2',
        'fine' => 'decimal:2',
        'employee_receivable' => 'decimal:2',
        'pph21_tax_object' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'take_home_pay' => 'decimal:2',
        'allowance' => 'decimal:2',
        'bonus' => 'decimal:2',
        'overtime' => 'decimal:2',
        'deduction' => 'decimal:2',
        'total_salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
