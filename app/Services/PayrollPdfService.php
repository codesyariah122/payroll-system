<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\PayrollTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PayrollPdfService
{
    public function generate(Payroll $payroll): string
    {
        $filename = sprintf(
            '%s-%s.pdf',
            Str::slug($payroll->employee->name),
            Str::slug($payroll->period, '-')
        );

        $path = sprintf(
            'payroll-slips/company-%s/%s/%s',
            $payroll->company_id ?: 'default',
            Str::slug($payroll->period, '-'),
            $filename
        );

        // hapus file lama jika ada
        Storage::disk('local')->delete($path);

        $payroll->load([
            'employee.department',
            'employee.position'
        ]);

        $template = PayrollTemplate::where('type', 'html')
            ->where('company_id', $payroll->company_id)
            ->where('is_active', true)
            ->first();

        $pdf = $template
            ? Pdf::loadHTML($this->renderHtmlTemplate($template->html_content, $payroll))
            : Pdf::loadView('payrolls.slip', [
                'payroll' => $payroll,
            ]);

        $pdf->setPaper('a4', 'portrait');

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    protected function renderHtmlTemplate(?string $html, Payroll $payroll): string
    {
        return strtr($html ?? '', $this->placeholders($payroll));
    }

    protected function placeholders(Payroll $payroll): array
    {
        $employee = $payroll->employee;

        return [
            '[[period]]' => (string) $payroll->period,
            '[[employee_name]]' => (string) $employee?->name,
            '[[employee_email]]' => (string) $employee?->email,
            '[[employee_nip]]' => (string) $employee?->nip,
            '[[position_name]]' => (string) ($payroll->position_name ?: $employee?->position?->name ?: '-'),
            '[[department_name]]' => (string) ($payroll->department_name ?: $employee?->department?->name ?: '-'),
            '[[target_work_days]]' => $this->number($payroll->target_work_days),
            '[[work_days]]' => $this->number($payroll->work_days),
            '[[overtime_hours]]' => $this->number($payroll->overtime_hours),
            '[[special_overtime_hours]]' => $this->number($payroll->special_overtime_hours),
            '[[basic_salary]]' => $this->rupiah($payroll->basic_salary),
            '[[position_allowance]]' => $this->rupiah($payroll->position_allowance),
            '[[attendance_allowance]]' => $this->rupiah($payroll->attendance_allowance),
            '[[safety_incentive]]' => $this->rupiah($payroll->safety_incentive),
            '[[risk_allowance]]' => $this->rupiah($payroll->risk_allowance),
            '[[placement_allowance]]' => $this->rupiah($payroll->placement_allowance),
            '[[golden_shake_hand]]' => $this->rupiah($payroll->golden_shake_hand),
            '[[tax_allowance]]' => $this->rupiah($payroll->tax_allowance),
            '[[irregular_income]]' => $this->rupiah($payroll->irregular_income),
            '[[overtime_pay]]' => $this->rupiah($payroll->overtime_pay),
            '[[total_income]]' => $this->rupiah($payroll->total_income),
            '[[bpjamsostek]]' => $this->rupiah($payroll->bpjamsostek),
            '[[bpjs_health]]' => $this->rupiah($payroll->bpjs_health),
            '[[attendance_deduction]]' => $this->rupiah($payroll->attendance_deduction),
            '[[fine]]' => $this->rupiah($payroll->fine),
            '[[employee_receivable]]' => $this->rupiah($payroll->employee_receivable),
            '[[pph21_tax_object]]' => $this->rupiah($payroll->pph21_tax_object),
            '[[total_deduction]]' => $this->rupiah($payroll->total_deduction),
            '[[take_home_pay]]' => $this->rupiah($payroll->take_home_pay),
        ];
    }

    protected function rupiah(mixed $value): string
    {
        return 'Rp. ' . number_format((float) $value, 0, ',', '.');
    }

    protected function number(mixed $value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }
}
