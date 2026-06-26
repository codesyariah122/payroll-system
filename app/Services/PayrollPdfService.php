<?php

namespace App\Services;

use App\Models\Payroll;
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
            'payroll-slips/%s/%s',
            Str::slug($payroll->period, '-'),
            $filename
        );

        // hapus file lama jika ada
        Storage::disk('local')->delete($path);

        $pdf = Pdf::loadView('payrolls.slip', [
            'payroll' => $payroll->load([
                'employee.department',
                'employee.position'
            ]),
        ])->setPaper('a4', 'portrait');

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }
}
