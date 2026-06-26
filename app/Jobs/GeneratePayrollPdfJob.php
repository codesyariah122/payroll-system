<?php

namespace App\Jobs;

use App\Models\Payroll;
use App\Services\PayrollPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePayrollPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Payroll $payroll
    ) {}

    public function handle(PayrollPdfService $pdfService): void
    {
        $this->payroll->pdf_path = $pdfService->generate($this->payroll);

        $this->payroll->save();
    }
}
