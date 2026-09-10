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

    public function test_employee_import_only_updates_master_employee_data(): void
    {
        $company = Company::create(['name' => 'HC Group']);
        $import = new EmployeeImport($company);

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

        $this->assertNotNull($employee);
        $this->assertNull($employee->user_id);
        $this->assertSame(0, Payroll::count());
    }

    public function test_employee_import_never_changes_existing_payroll(): void
    {
        $company = Company::create(['name' => 'HC Group']);
        $import = new EmployeeImport($company);

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

        $this->assertSame(0, Payroll::count());
    }

    public function test_payroll_import_service_preserves_other_periods(): void
    {
        $company = Company::create(['name' => 'HC Group']);
        $employeeImport = new EmployeeImport($company);
        $employeeImport->collection(collect([
            new Collection([
                'nama' => 'Tia Nurul Safitri',
                'email' => 'tia@example.com',
                'departemen' => 'Factory',
                'jabatan' => 'Operator',
            ]),
        ]));

        $employee = Employee::where('company_id', $company->id)->firstOrFail();
        $service = app(PayrollService::class);

        $service->createOrUpdatePayroll($employee, [
            'period' => 'Juli 2026', 'basic_salary' => 3000000, 'take_home_pay' => 3000000,
        ]);
        $service->createOrUpdatePayroll($employee, [
            'period' => 'Agustus 2026', 'basic_salary' => 3200000, 'take_home_pay' => 3200000,
        ]);
        $service->createOrUpdatePayroll($employee, [
            'period' => 'Agustus 2026', 'basic_salary' => 3250000, 'take_home_pay' => 3250000,
        ]);

        $this->assertSame(2, Payroll::count());
        $this->assertSame('3000000.00', Payroll::where('period', 'Juli 2026')->value('take_home_pay'));
        $this->assertSame('3250000.00', Payroll::where('period', 'Agustus 2026')->value('take_home_pay'));
    }
}
