<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Support\OrganizationLookup;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('payroll:repair-snapshots {file}', function (string $file) {
    if (! file_exists($file)) {
        $this->error("File tidak ditemukan: {$file}");

        return Command::FAILURE;
    }

    $sheet = IOFactory::load($file)->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);
    $headerRow = array_shift($rows);

    $headers = [];

    foreach ($headerRow as $column => $label) {
        $key = strtolower(trim((string) $label));
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);
        $headers[$column] = trim($key, '_');
    }

    $updatedPayrolls = 0;
    $updatedEmployees = 0;

    foreach ($rows as $row) {
        $data = [];

        foreach ($headers as $column => $key) {
            if ($key !== '') {
                $data[$key] = $row[$column] ?? null;
            }
        }

        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $period = trim((string) ($data['bulan'] ?? $data['period'] ?? $data['periode'] ?? ''));
        $positionColumnExists = array_key_exists('jabatan', $data) || array_key_exists('posisi', $data);
        $departmentColumnExists = array_key_exists('departemen', $data) || array_key_exists('department', $data);

        $positionName = trim((string) ($data['jabatan'] ?? $data['posisi'] ?? ''));
        $departmentName = trim((string) ($data['departemen'] ?? $data['department'] ?? ''));

        $positionName = preg_replace('/\s+/', ' ', $positionName);
        $departmentName = preg_replace('/\s+/', ' ', $departmentName);

        if ($positionColumnExists && $positionName === '') {
            $positionName = '-';
        }

        if ($departmentColumnExists && $departmentName === '') {
            $departmentName = '-';
        }

        if ($email === '' || $period === '' || ($positionName === '' && $departmentName === '')) {
            continue;
        }

        $employee = Employee::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();

        if (! $employee) {
            continue;
        }

        $employeeUpdates = [];

        if ($positionName !== '' && $positionName !== '-') {
            $employeeUpdates['position_id'] = OrganizationLookup::position($positionName)->id;
        }

        if ($departmentName !== '' && $departmentName !== '-') {
            $employeeUpdates['department_id'] = OrganizationLookup::department($departmentName)->id;
        }

        if (! empty($employeeUpdates)) {
            $employee->update($employeeUpdates);
            $updatedEmployees++;
        }

        $payrollUpdates = [
            'pdf_path' => null,
        ];

        if ($positionName !== '') {
            $payrollUpdates['position_name'] = $positionName;
        }

        if ($departmentName !== '') {
            $payrollUpdates['department_name'] = $departmentName;
        }

        $updatedPayrolls += Payroll::where('employee_id', $employee->id)
            ->where('period', $period)
            ->update($payrollUpdates);
    }

    Storage::disk('local')->deleteDirectory('payroll-slips');

    $this->info("Repair selesai. Payroll diperbarui: {$updatedPayrolls}. Employee diperbarui: {$updatedEmployees}.");

    return Command::SUCCESS;
})->purpose('Repair payroll position and department snapshots from an Excel payroll file');

Artisan::command('organization:merge-duplicates', function () {
    $mergedDepartments = 0;
    $mergedPositions = 0;

    Department::orderBy('id')->get()->groupBy(fn (Department $department) => OrganizationLookup::normalizeName($department->name))
        ->each(function ($departments) use (&$mergedDepartments) {
            $primary = $departments->first();

            $departments->skip(1)->each(function (Department $duplicate) use ($primary, &$mergedDepartments) {
                Employee::where('department_id', $duplicate->id)->update(['department_id' => $primary->id]);
                $duplicate->delete();
                $mergedDepartments++;
            });
        });

    Position::orderBy('id')->get()->groupBy(fn (Position $position) => OrganizationLookup::normalizeName($position->name))
        ->each(function ($positions) use (&$mergedPositions) {
            $primary = $positions->first();

            $positions->skip(1)->each(function (Position $duplicate) use ($primary, &$mergedPositions) {
                Employee::where('position_id', $duplicate->id)->update(['position_id' => $primary->id]);
                $duplicate->delete();
                $mergedPositions++;
            });
        });

    $this->info("Merge selesai. Department duplikat digabung: {$mergedDepartments}. Posisi duplikat digabung: {$mergedPositions}.");

    return Command::SUCCESS;
})->purpose('Merge duplicate departments and positions by normalized names');
