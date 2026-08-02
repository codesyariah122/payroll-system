<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Support\OrganizationLookup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToCollection, WithHeadingRow, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    protected const EMAIL_ALIASES = ['email', 'e_mail', 'email_karyawan', 'alamat_email'];

    protected const NAME_ALIASES = ['nama', 'name', 'nama_karyawan', 'employee_name'];

    protected const DEPARTMENT_ALIASES = ['departemen', 'department', 'bagian', 'divisi'];

    protected const POSITION_ALIASES = ['jabatan', 'posisi', 'position', 'jabatan_posisi'];

    protected const NIP_ALIASES = ['nip', 'no_induk', 'employee_id'];

    protected const BASIC_SALARY_ALIASES = ['gaji_pokok', 'basic_salary'];

    protected const ALLOWANCE_ALIASES = ['tunjangan_jabatan', 'allowance'];

    protected const PHONE_ALIASES = ['phone', 'telepon', 'no_hp', 'nomor_hp'];

    protected const ADDRESS_ALIASES = ['address', 'alamat'];

    protected const JOIN_DATE_ALIASES = ['join_date', 'tanggal_masuk', 'tgl_masuk'];

    protected const STATUS_ALIASES = ['status'];

    protected array $departmentCache = [];

    protected array $positionCache = [];

    protected array $usedNips = [];

    protected int $importedCount = 0;

    protected int $skippedCount = 0;

    public function __construct(protected Company $company)
    {
        HeadingRowFormatter::extend('custom', function ($value) {
            $value = strtolower(trim((string) $value));
            $value = preg_replace('/[^a-z0-9]+/', '_', $value);

            return trim($value, '_');
        });

        HeadingRowFormatter::default('custom');

        $this->usedNips = Employee::where('company_id', $this->company->id)
            ->whereNotNull('nip')
            ->pluck('nip')
            ->filter()
            ->flip()
            ->all();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows->filter(fn (Collection $row) => $row->filter()->isNotEmpty()) as $row) {

            $email = strtolower(trim((string) $this->valueFromRow($row, self::EMAIL_ALIASES)));

            if (empty($email)) {
                $this->skippedCount++;

                continue;
            }

            if (Employee::where('email', $email)->where('company_id', '!=', $this->company->id)->exists()) {
                $this->skippedCount++;

                continue;
            }

            $name = trim((string) ($this->valueFromRow($row, self::NAME_ALIASES) ?: 'Unknown'));

            $departmentName = trim((string) ($this->valueFromRow($row, self::DEPARTMENT_ALIASES) ?: 'General'));
            $positionName = trim((string) ($this->valueFromRow($row, self::POSITION_ALIASES) ?: 'Staff'));

            $departmentName = OrganizationLookup::cleanName($departmentName, 'General');
            $positionName = OrganizationLookup::cleanName($positionName, 'Staff');

            $department = $this->department($departmentName);
            $position = $this->position($positionName);

            $employee = Employee::where('company_id', $this->company->id)
                ->where('email', $email)
                ->first();

            if (! $employee && Employee::where('email', $email)->exists()) {
                $this->skippedCount++;

                continue;
            }

            $employee ??= new Employee(['email' => $email]);

            if (! $employee->exists) {
                $nip = trim((string) $this->valueFromRow($row, self::NIP_ALIASES));

                $employee->nip = $nip !== '' && ! isset($this->usedNips[$nip])
                    ? $this->rememberNip($nip)
                    : $this->generateUniqueNip();

                $employee->join_date = $this->dateValue($this->valueFromRow($row, self::JOIN_DATE_ALIASES))
                    ?: now()->format('Y-m-d');
            }

            $employee->company_id = $this->company->id;
            $employee->user_id ??= null;
            $employee->department_id = $department->id;
            $employee->position_id = $position->id;
            $employee->name = $name;
            $employee->email = $email;
            $employee->phone = trim((string) ($this->valueFromRow($row, self::PHONE_ALIASES) ?? $employee->phone ?? ''));
            $employee->address = trim((string) ($this->valueFromRow($row, self::ADDRESS_ALIASES) ?? $employee->address ?? ''));

            $basicSalary = $this->valueFromRow($row, self::BASIC_SALARY_ALIASES);
            if ($basicSalary !== null && $basicSalary !== '') {
                $employee->basic_salary = $this->numberValue($basicSalary);
            }

            $allowance = $this->valueFromRow($row, self::ALLOWANCE_ALIASES);
            if ($allowance !== null && $allowance !== '') {
                $employee->allowance = $this->numberValue($allowance);
            }

            $status = strtolower(trim((string) ($this->valueFromRow($row, self::STATUS_ALIASES) ?: 'active')));
            $employee->status = in_array($status, ['active', 'inactive'], true) ? $status : 'active';

            $employee->save();
            $this->importedCount++;
        }
    }

    public function summary(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
        ];
    }

    protected function department(string $name): Department
    {
        $key = OrganizationLookup::normalizeName($name);

        return $this->departmentCache[$key] ??= OrganizationLookup::department($name, $this->company->id);
    }

    protected function position(string $name): Position
    {
        $key = OrganizationLookup::normalizeName($name);

        return $this->positionCache[$key] ??= OrganizationLookup::position($name, $this->company->id);
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

    protected function numberValue(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = preg_replace('/[^0-9,.-]/', '', (string) $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float) $value : 0;
    }

    protected function dateValue(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function rememberNip(string $nip): string
    {
        $this->usedNips[$nip] = true;

        return $nip;
    }

    private function generateUniqueNip(): string
    {
        do {

            $nip = 'EMP-' . str_pad(
                mt_rand(1, 99999),
                5,
                '0',
                STR_PAD_LEFT
            );
        } while (isset($this->usedNips[$nip]));

        return $this->rememberNip($nip);
    }
}
