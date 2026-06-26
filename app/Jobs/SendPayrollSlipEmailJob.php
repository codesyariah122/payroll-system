<?php

namespace App\Jobs;

use App\Mail\PayrollSlipMail;
use App\Models\Payroll;
use App\Services\PayrollPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPayrollSlipEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    protected int $payrollId;

    public function __construct(Payroll $payroll)
    {
        $this->payrollId = $payroll->id;
    }

    public function handle(PayrollPdfService $pdfService): void
    {
        try {

            $payroll = Payroll::with('employee')
                ->findOrFail($this->payrollId);

            if (in_array($payroll->email_status, ['sent', 'sending'], true)) {

                Log::info('PAYROLL EMAIL SKIPPED', [
                    'payroll_id' => $payroll->id,
                    'email_status' => $payroll->email_status,
                ]);

                return;
            }

            $updated = Payroll::where('id', $payroll->id)
                ->where(function ($query) {
                    $query->whereNull('email_status')
                        ->orWhereIn('email_status', ['pending', 'failed', 'queued']);
                })
                ->update([
                    'email_status' => 'sending',
                    'email_error' => null,
                ]);

            if ($updated === 0) {
                Log::info('PAYROLL EMAIL LOCK SKIPPED', [
                    'payroll_id' => $payroll->id,
                ]);

                return;
            }

            $payroll->pdf_path = $pdfService->generate($payroll);
            $payroll->save();

            Log::info('BEFORE SEND', [
                'payroll_id' => $payroll->id,
            ]);

            Mail::to($payroll->employee->email)
                ->send(new PayrollSlipMail($payroll));

            Log::info('AFTER SEND', [
                'payroll_id' => $payroll->id,
            ]);

            Payroll::where('id', $payroll->id)
                ->update([
                    'email_status' => 'sent',
                    'email_sent_at' => now(),
                    'email_error' => null,
                ]);

            Log::info('AFTER UPDATE SENT', [
                'payroll_id' => $payroll->id,
            ]);

            Log::info('PAYROLL EMAIL SENT', [
                'payroll_id' => $payroll->id,
                'email' => $payroll->employee->email,
            ]);
        } catch (\Throwable $e) {

            Log::error('PAYROLL EMAIL FAILED', [
                'payroll_id' => $this->payrollId,
                'message' => $e->getMessage(),
            ]);

            Payroll::where('id', $this->payrollId)
                ->update([
                    'email_status' => 'failed',
                    'email_error' => $e->getMessage(),
                ]);

            throw $e;
        }
    }
}
