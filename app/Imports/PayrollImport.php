<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Services\PayrollService;
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
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class PayrollImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsOnError,
    WithCalculatedFormulas
{
    use Importable, SkipsFailures, SkipsErrors;

    public function __construct(protected PayrollService $payrollService)
    {
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
        Log::info('TOTAL ROWS IMPORT', [
            'count' => $rows->count(),
        ]);

        foreach ($rows as $row) {

            Log::info('ROW IMPORT', $row->toArray());

            if ($row->filter()->isEmpty()) {
                continue;
            }

            $employee = $this->findEmployee($row);

            if (! $employee) {
                continue;
            }

            $this->syncEmployeeOrganization($employee, $row);

            $this->payrollService->createPayroll(
                $employee,
                $this->payrollData($row)
            );
        }
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'bulan' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'email.required' => 'Kolom email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ];
    }

    protected function findEmployee(Collection $row): ?Employee
    {
        $email = strtolower(trim((string) ($row['email'] ?? '')));

        if ($email === '') {

            Log::warning('EMAIL KOSONG', [
                'row' => $row->toArray(),
            ]);

            return null;
        }

        $employee = Employee::whereRaw(
            'LOWER(TRIM(email)) = ?',
            [$email]
        )->first();

        if (! $employee) {

            Log::warning('EMAIL TIDAK DITEMUKAN', [
                'email_excel' => $email,
            ]);
        }

        return $employee;
    }

    protected function syncEmployeeOrganization(Employee $employee, Collection $row): void
    {
        $positionName = trim((string) ($row['jabatan'] ?? $row['posisi'] ?? ''));
        $departmentName = trim((string) ($row['departemen'] ?? $row['department'] ?? ''));

        $positionName = preg_replace('/\s+/', ' ', $positionName);
        $departmentName = preg_replace('/\s+/', ' ', $departmentName);

        $updates = [];

        if ($positionName !== '') {
            $updates['position_id'] = Position::firstOrCreate([
                'name' => $positionName,
            ])->id;
        }

        if ($departmentName !== '') {
            $updates['department_id'] = Department::firstOrCreate([
                'name' => $departmentName,
            ])->id;
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

            'position_name' => $this->textValueFromRow($row, ['jabatan', 'posisi']),
            'department_name' => $this->textValueFromRow($row, ['departemen', 'department']),

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
