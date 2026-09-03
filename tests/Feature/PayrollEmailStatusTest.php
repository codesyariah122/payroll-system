<?php

namespace Tests\Feature;

use App\Jobs\SendPayrollSlipEmailJob;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Services\PayrollPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class PayrollEmailStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_email_is_marked_failed_when_smtp_send_fails(): void
    {
        $payroll = $this->makePayroll(['email_status' => 'queued']);

        $this->mock(PayrollPdfService::class, function ($mock) {
            $mock->shouldReceive('generate')->once()->andReturn('payroll-slips/test.pdf');
        });

        Mail::shouldReceive('to')
            ->once()
            ->with('employee@example.com')
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->andThrow(new RuntimeException('SMTP rejected recipient'));

        try {
            app(SendPayrollSlipEmailJob::class, ['payroll' => $payroll])->handle(app(PayrollPdfService::class));
            $this->fail('Expected SMTP failure was not thrown.');
        } catch (RuntimeException) {
            //
        }

        $payroll->refresh();

        $this->assertSame('failed', $payroll->email_status);
        $this->assertNull($payroll->email_sent_at);
        $this->assertSame('SMTP rejected recipient', $payroll->email_error);
    }

    public function test_failed_callback_keeps_payroll_out_of_sent_status(): void
    {
        $payroll = $this->makePayroll([
            'email_status' => 'sending',
            'email_sent_at' => now(),
        ]);

        $job = new SendPayrollSlipEmailJob($payroll);
        $job->failed(new RuntimeException('Worker timeout'));

        $payroll->refresh();

        $this->assertSame('failed', $payroll->email_status);
        $this->assertNull($payroll->email_sent_at);
        $this->assertSame('Worker timeout', $payroll->email_error);
    }

    protected function makePayroll(array $overrides = []): Payroll
    {
        $company = Company::create(['name' => 'Test Company']);
        $department = Department::create(['company_id' => $company->id, 'name' => 'HC']);
        $position = Position::create(['company_id' => $company->id, 'name' => 'Staff']);

        $employee = Employee::create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'nip' => 'EMP-001',
            'name' => 'Employee Test',
            'email' => 'employee@example.com',
            'phone' => '',
            'address' => '',
            'join_date' => now()->toDateString(),
            'basic_salary' => 1000000,
            'allowance' => 0,
            'status' => 'active',
        ]);

        return Payroll::create(array_merge([
            'company_id' => $company->id,
            'employee_id' => $employee->id,
            'period' => 'September 2026',
            'target_work_days' => 22,
            'work_days' => 22,
            'overtime_hours' => 0,
            'special_overtime_hours' => 0,
            'basic_salary' => 1000000,
            'position_allowance' => 0,
            'attendance_allowance' => 0,
            'safety_incentive' => 0,
            'risk_allowance' => 0,
            'placement_allowance' => 0,
            'golden_shake_hand' => 0,
            'tax_allowance' => 0,
            'irregular_income' => 0,
            'overtime_pay' => 0,
            'total_income' => 1000000,
            'bpjamsostek' => 0,
            'bpjs_health' => 0,
            'attendance_deduction' => 0,
            'fine' => 0,
            'employee_receivable' => 0,
            'pph21_tax_object' => 0,
            'total_deduction' => 0,
            'take_home_pay' => 1000000,
            'allowance' => 0,
            'bonus' => 0,
            'overtime' => 0,
            'deduction' => 0,
            'total_salary' => 1000000,
            'email_status' => 'pending',
            'email_error' => null,
            'email_sent_at' => null,
        ], $overrides));
    }
}
