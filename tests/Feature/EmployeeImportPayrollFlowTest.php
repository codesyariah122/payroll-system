<?php

namespace Tests\Feature;

use App\Imports\EmployeeImport;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EmployeeImportPayrollFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_import_creates_payroll_from_the_same_row(): void
    {
        $company = Company::create(['name' => 'HC Group']);
        $import = new EmployeeImport($company, app(PayrollService::class));

        $import->collection(collect([
            new Collection([
                'nama' => 'Tia Nurul Safitri',
                'email' => 'tia@example.com',
                'departemen' => 'Factory & Supply Chain',
                'jabatan' => 'Production Operator',
                'bulan' => '21 Juli - 20 Agustus 2026',
                'target_hk' => 26,
                'hk' => 24,
                'gaji_pokok' => 3500000,
                'tunjangan_jabatan' => 500000,
                'uang_lembur' => 250000,
                'jumlah_potongan' => 100000,
                'thp_gaji_bersih' => 4150000,
            ]),
        ]));

        $employee = Employee::where('company_id', $company->id)
            ->where('email', 'tia@example.com')
            ->first();

        $payroll = Payroll::where('company_id', $company->id)
            ->where('employee_id', $employee?->id)
            ->where('period', '21 Juli - 20 Agustus 2026')
            ->first();

        $summary = $import->summary();

        $this->assertNotNull($employee);
        $this->assertNull($employee->user_id);
        $this->assertNotNull($payroll);
        $this->assertSame('4150000.00', $payroll->take_home_pay);
        $this->assertSame(1, $summary['payroll']['created']);
        $this->assertSame(0, $summary['payroll']['updated']);
    }

    public function test_employee_import_updates_existing_payroll_for_same_employee_and_period(): void
    {
        $company = Company::create(['name' => 'HC Group']);
        $import = new EmployeeImport($company, app(PayrollService::class));

        $row = fn (int $salary) => new Collection([
            'nama' => 'Tia Nurul Safitri',
            'email' => 'tia@example.com',
            'departemen' => 'Factory & Supply Chain',
            'jabatan' => 'Production Operator',
            'bulan' => '21 Juli - 20 Agustus 2026',
            'target_hk' => 26,
            'hk' => 24,
            'gaji_pokok' => $salary,
            'thp_gaji_bersih' => $salary,
        ]);

        $import->collection(collect([$row(3500000)]));
        $import->collection(collect([$row(3750000)]));

        $this->assertSame(1, Payroll::count());
        $this->assertSame('3750000.00', Payroll::first()->take_home_pay);
        $this->assertSame(1, $import->summary()['payroll']['updated']);
    }
}
