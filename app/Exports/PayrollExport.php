<?php

namespace App\Exports;

use App\Models\Payroll;
use App\Support\PayrollSlipFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayrollExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Payroll::with(['employee.department', 'employee.position'])
            ->get()
            ->values()
            ->map(function ($payroll, $index) {
                return PayrollSlipFormat::exportRow($payroll, $index + 1);
            });
    }

    public function headings(): array
    {
        return PayrollSlipFormat::headings();
    }
}
