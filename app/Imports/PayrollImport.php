<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Employee;
use App\Services\PayrollService;
use App\Support\OrganizationLookup;
use App\Support\PayrollSlipFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class PayrollImport implements
    ToCollection,
    WithHeadingRow,
    SkipsOnFailure,
    SkipsOnError,
    WithCalculatedFormulas
{
    use Importable, SkipsFailures, SkipsErrors;

    protected const EMAIL_ALIASES = ['email', 'e_mail', 'email_karyawan', 'alamat_email'];

    protected const POSITION_ALIASES = ['jabatan', 'posisi', 'position', 'jabatan_posisi'];

    protected const DEPARTMENT_ALIASES = ['departemen', 'department', 'bagian', 'divisi'];

    public const REQUIRED_HEADERS = [
        'Email' => self::EMAIL_ALIASES,
        'Bulan / Periode' => PayrollSlipFormat::IMPORT_ALIASES['period'],
        'Target Hari Kerja' => PayrollSlipFormat::IMPORT_ALIASES['target_work_days'],
        'Hari Kerja' => PayrollSlipFormat::IMPORT_ALIASES['work_days'],
        'Gaji Pokok' => PayrollSlipFormat::IMPORT_ALIASES['basic_salary'],
        'Total Gaji Bersih / THP' => PayrollSlipFormat::IMPORT_ALIASES['take_home_pay'],
    ];

    protected int $createdCount = 0;

    protected int $updatedCount = 0;

    protected int $skippedCount = 0;

    protected array $importErrors = [];

    protected array $warnings = [];

    protected bool $payrollHeadersDetected = false;

    public function __construct(
        protected PayrollService $payrollService,
        protected Company $company,
        protected bool $requirePayrollHeaders = true
    ) {
        HeadingRowFormatter::extend('custom', function ($value) {

            $value = strtolower(trim($value));

            $value = preg_replace('/[^a-z0-9]+/', '_', $value);

            $value = trim($value, '_');

            return $value;
        });

        HeadingRowFormatter::default('custom');
    }

    public function collection(Collection $rows): void
    {
        $rows = $rows->filter(fn(Collection $row) => $row->filter()->isNotEmpty())->values();

        Log::info('TOTAL ROWS IMPORT', [
            'count' => $rows->count(),
        ]);

        if ($rows->isEmpty()) {
            $this->importErrors[] = 'File Excel tidak memiliki data payroll.';

            return;
        }

        $this->validateRequiredHeaders($rows->first());

        if (! $this->payrollHeadersDetected && ! $this->requirePayrollHeaders) {
            return;
        }

        if (! empty($this->importErrors)) {
            return;
        }

        foreach ($rows as $index => $row) {

            Log::info('ROW IMPORT', $row->toArray());

            $rowNumber = $index + 2;
            $employee = $this->findEmployee($row, $rowNumber);

            if (! $employee) {
                $this->skippedCount++;

                continue;
            }

            $this->syncEmployeeOrganization($employee, $row);

            [, $created] = $this->payrollService->createOrUpdatePayroll(
                $employee,
                $this->payrollData($row)
            );

            $created ? $this->createdCount++ : $this->updatedCount++;
        }
    }

    public function summary(): array
    {
        return [
            'created' => $this->createdCount,
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->importErrors,
            'warnings' => $this->warnings,
            'payroll_headers_detected' => $this->payrollHeadersDetected,
        ];
    }

    protected function findEmployee(Collection $row, int $rowNumber): ?Employee
    {
        $email = strtolower(trim((string) $this->valueFromRow($row, self::EMAIL_ALIASES)));

        if ($email === '') {

            $this->warnings[] = "Baris {$rowNumber}: email kosong, data dilewati.";

            Log::warning('EMAIL KOSONG', [
                'row' => $row->toArray(),
            ]);

            return null;
        }

        $employee = Employee::whereRaw(
            'LOWER(TRIM(email)) = ?',
            [$email]
        )
            ->where('company_id', $this->company->id)
            ->first();

        if (! $employee) {

            $this->warnings[] = "Baris {$rowNumber}: email {$email} tidak ditemukan di data karyawan.";

            Log::warning('EMAIL TIDAK DITEMUKAN', [
                'email_excel' => $email,
            ]);
        }

        return $employee;
    }

    protected function syncEmployeeOrganization(Employee $employee, Collection $row): void
    {
        $positionName = trim((string) $this->valueFromRow($row, self::POSITION_ALIASES));
        $departmentName = trim((string) $this->valueFromRow($row, self::DEPARTMENT_ALIASES));

        $positionName = OrganizationLookup::cleanName($positionName, '');
        $departmentName = OrganizationLookup::cleanName($departmentName, '');

        $updates = [];

        if ($positionName !== '') {
            $updates['position_id'] = OrganizationLookup::position($positionName, $this->company->id)->id;
        }

        if ($departmentName !== '') {
            $updates['department_id'] = OrganizationLookup::department($departmentName, $this->company->id)->id;
        }

        if (! empty($updates)) {
            $employee->update($updates);
            $employee->refresh();
        }
    }

    protected function payrollData(Collection $row): array
    {
        Log::info('PAYROLL IMPORT ROW', $row->toArray());

        return [

            'position_name' => $this->textValueFromRow($row, self::POSITION_ALIASES),
            'department_name' => $this->textValueFromRow($row, self::DEPARTMENT_ALIASES),

            'period' => trim((string) $this->valueFromRow(
                $row,
                PayrollSlipFormat::IMPORT_ALIASES['period']
            )),

            'target_work_days' => (int) $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['target_work_days'])
            ),

            'work_days' => (int) $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['work_days'])
            ),

            'overtime_hours' => (int) $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['overtime_hours'])
            ),

            'special_overtime_hours' => (int) $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['special_overtime_hours'])
            ),

            'basic_salary' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['basic_salary'])
            ),

            'position_allowance' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['position_allowance'])
            ),

            'attendance_allowance' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['attendance_allowance'])
            ),

            'safety_incentive' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['safety_incentive'])
            ),

            'risk_allowance' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['risk_allowance'])
            ),

            'placement_allowance' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['placement_allowance'])
            ),

            'golden_shake_hand' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['golden_shake_hand'])
            ),

            'tax_allowance' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['tax_allowance'])
            ),

            'irregular_income' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['irregular_income'])
            ),

            'overtime_pay' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['overtime_pay'])
            ),

            'total_income' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['total_income'])
            ),

            'bpjamsostek' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['bpjamsostek'])
            ),

            'bpjs_health' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['bpjs_health'])
            ),

            'attendance_deduction' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['attendance_deduction'])
            ),

            'fine' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['fine'])
            ),

            'employee_receivable' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['employee_receivable'])
            ),

            'pph21_tax_object' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['pph21_tax_object'])
            ),

            'total_deduction' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['total_deduction'])
            ),

            'take_home_pay' => $this->numberValue(
                $this->valueFromRow($row, PayrollSlipFormat::IMPORT_ALIASES['take_home_pay'])
            ),
        ];
    }

    protected function valueFromRow(Collection $row, array $aliases): mixed
    {
        foreach ($aliases as $alias) {

            if ($row->has($alias) && $row[$alias] !== null) {
                return $row[$alias];
            }
        }

        return null;
    }

    protected function validateRequiredHeaders(Collection $row): void
    {
        $headers = $row->keys()->filter(fn($header) => is_string($header) && $header !== '')->all();
        $missingHeaders = [];

        foreach (self::REQUIRED_HEADERS as $label => $aliases) {
            if ($this->hasAnyHeader($headers, $aliases)) {
                $this->payrollHeadersDetected = true;

                continue;
            }

            $missingHeaders[] = sprintf(
                'Header "%s" tidak ditemukan. Nama yang didukung: %s.',
                $label,
                implode(', ', $aliases)
            );
        }

        if (! $this->payrollHeadersDetected && ! $this->requirePayrollHeaders) {
            return;
        }

        $this->importErrors = array_merge($this->importErrors, $missingHeaders);
    }

    protected function hasAnyHeader(array $headers, array $aliases): bool
    {
        foreach ($aliases as $alias) {
            if (in_array($alias, $headers, true)) {
                return true;
            }
        }

        return false;
    }

    protected function textValueFromRow(Collection $row, array $aliases): ?string
    {
        foreach ($aliases as $alias) {
            if ($row->has($alias)) {
                $value = trim((string) ($row[$alias] ?? ''));
                $value = preg_replace('/\s+/', ' ', $value);

                return $value !== '' ? $value : '-';
            }
        }

        return null;
    }

    protected function numberValue(mixed $value): float
    {
        if (is_numeric($value)) {
            return floatval($value);
        }

        $value = preg_replace('/[^0-9,.-]/', '', (string) $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {

            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {

            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value)
            ? floatval($value)
            : 0;
    }
}
