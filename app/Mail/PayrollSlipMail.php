<?php

namespace App\Mail;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PayrollSlipMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payroll $payroll) {}

    public function build()
    {
        $pdfPath = Storage::disk('local')->path($this->payroll->pdf_path);

        return $this->subject("Slip Gaji {$this->payroll->period}")
            ->view('emails.payroll-slip')
            ->with([
                'payroll' => $this->payroll->load(['employee.department', 'employee.position']),
            ])
            ->attach($pdfPath, [
                'as' => sprintf('slip-gaji-%s-%s.pdf', $this->payroll->employee->nip, str_replace('/', '-', $this->payroll->period)),
                'mime' => 'application/pdf',
            ]);
    }
}
