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
use Throwable;

class SendPayrollSlipEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    protected int $payrollId;

    protected ?string $recipientOverride;

    public function __construct(Payroll $payroll, ?string $recipientOverride = null)
    {
        $this->payrollId = $payroll->id;
        $this->recipientOverride = $recipientOverride;
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

            if ((float) $payroll->take_home_pay <= 0) {
                Payroll::where('id', $payroll->id)
                    ->update([
                        'email_status' => 'skipped',
                        'email_error' => 'Total gaji bersih 0, email tidak dikirim.',
                    ]);

                Log::info('PAYROLL EMAIL SKIPPED ZERO SALARY', [
                    'payroll_id' => $payroll->id,
                    'take_home_pay' => $payroll->take_home_pay,
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

            $recipient = $this->recipientOverride ?: $payroll->employee->email;

            Mail::to($recipient)
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
                'email' => $recipient,
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

    public function failed(?Throwable $exception): void
    {
        Payroll::where('id', $this->payrollId)
            ->update([
                'email_status' => 'failed',
                'email_sent_at' => null,
                'email_error' => $exception?->getMessage() ?: 'Email job failed before SMTP success was confirmed.',
            ]);
    }
}
