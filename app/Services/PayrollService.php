<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Support\PayrollSlipFormat;
use App\Jobs\GeneratePayrollPdfJob;

class PayrollService
{
    public function __construct(protected PayrollPdfService $pdfService) {}

    public function calculateTotal(array $data): float
    {
        return $this->normalizePayrollData($data)['take_home_pay'];
    }

    public function normalizePayrollData(array $data): array
    {
        foreach (PayrollSlipFormat::NUMERIC_FIELDS as $field) {
            $data[$field] = floatval($data[$field] ?? 0);
        }

        $data['total_income'] = $data['total_income'] ?: array_sum(
            array_map(fn($field) => $data[$field], PayrollSlipFormat::EARNING_FIELDS)
        );

        $data['total_deduction'] = $data['total_deduction'] ?: array_sum(
            array_map(fn($field) => $data[$field], PayrollSlipFormat::DEDUCTION_FIELDS)
        );

        $data['take_home_pay'] = $data['take_home_pay'] ?: $data['total_income'] - $data['total_deduction'];

        $data['allowance'] = $data['allowance'] ?? (
            $data['position_allowance'] +
            $data['attendance_allowance'] +
            $data['safety_incentive'] +
            $data['risk_allowance'] +
            $data['placement_allowance'] +
            $data['golden_shake_hand'] +
            $data['tax_allowance']
        );
        $data['bonus'] = $data['bonus'] ?? $data['irregular_income'];
        $data['overtime'] = $data['overtime'] ?? $data['overtime_pay'];
        $data['deduction'] = $data['deduction'] ?? $data['total_deduction'];
        $data['total_salary'] = $data['total_salary'] ?? $data['take_home_pay'];

        return $data;
    }

    public function createPayroll(Employee $employee, array $data): Payroll
    {
        $data = $this->normalizePayrollData($data);

        $payroll = Payroll::create($this->payrollPayload($employee, $data));

        $payroll->refresh();

        return $payroll;
    }

    public function createOrUpdatePayroll(Employee $employee, array $data): array
    {
        $data = $this->normalizePayrollData($data);

        $payroll = Payroll::where('company_id', $employee->company_id)
            ->where('employee_id', $employee->id)
            ->where('period', $data['period'])
            ->first();

        $created = ! $payroll;
        $payroll ??= new Payroll();

        $payroll->fill($this->payrollPayload($employee, $data));

        if (! $created) {
            $payroll->fill([
                'pdf_path' => null,
                'email_status' => 'pending',
                'email_error' => null,
                'email_sent_at' => null,
            ]);
        }

        $payroll->save();
        $payroll->refresh();

        return [$payroll, $created];
    }

    protected function payrollPayload(Employee $employee, array $data): array
    {
        return [
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,

            'position_name' => !empty($data['position_name'])
                ? $data['position_name']
                : $employee->position?->name,

            'department_name' => !empty($data['department_name'])
                ? $data['department_name']
                : $employee->department?->name,

            'period' => $data['period'],
            'target_work_days' => $data['target_work_days'],
            'work_days' => $data['work_days'],
            'overtime_hours' => $data['overtime_hours'],
            'special_overtime_hours' => $data['special_overtime_hours'],
            'basic_salary' => $data['basic_salary'],
            'position_allowance' => $data['position_allowance'],
            'attendance_allowance' => $data['attendance_allowance'],
            'safety_incentive' => $data['safety_incentive'],
            'risk_allowance' => $data['risk_allowance'],
            'placement_allowance' => $data['placement_allowance'],
            'golden_shake_hand' => $data['golden_shake_hand'],
            'tax_allowance' => $data['tax_allowance'],
            'irregular_income' => $data['irregular_income'],
            'overtime_pay' => $data['overtime_pay'],
            'total_income' => $data['total_income'],
            'bpjamsostek' => $data['bpjamsostek'],
            'bpjs_health' => $data['bpjs_health'],
            'attendance_deduction' => $data['attendance_deduction'],
            'fine' => $data['fine'],
            'employee_receivable' => $data['employee_receivable'],
            'pph21_tax_object' => $data['pph21_tax_object'],
            'total_deduction' => $data['total_deduction'],
            'take_home_pay' => $data['take_home_pay'],
            'allowance' => $data['allowance'],
            'bonus' => $data['bonus'],
            'overtime' => $data['overtime'],
            'deduction' => $data['deduction'],
            'total_salary' => $data['total_salary'],
        ];
    }

    public function createPayrollWithPdf(Employee $employee, array $data): Payroll
    {
        $payroll = $this->createPayroll($employee, $data);

        GeneratePayrollPdfJob::dispatch($payroll);

        return $payroll;
    }

    public function getPdfService(): PayrollPdfService
    {
        return $this->pdfService;
    }
}
