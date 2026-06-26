<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
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

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {

            if ($row->filter()->isEmpty()) {
                continue;
            }

            $email = strtolower(trim($row['email'] ?? ''));

            if (empty($email)) {
                continue;
            }

            $name = trim($row['nama'] ?? 'Unknown');

            $departmentName = trim($row['departemen'] ?? 'General');
            $positionName   = trim($row['jabatan'] ?? $row['posisi'] ?? 'Staff');

            // rapikan spasi berlebih
            $departmentName = preg_replace('/\s+/', ' ', $departmentName);
            $positionName   = preg_replace('/\s+/', ' ', $positionName);

            // DEPARTMENT
            $department = Department::firstOrCreate([
                'name' => $departmentName,
            ]);

            // POSITION
            $position = Position::firstOrCreate([
                'name' => $positionName,
            ]);

            // USER
            $user = User::firstOrNew([
                'email' => $email,
            ]);

            $user->name = $name;
            $user->email = $email;
            $user->role = 'employee';

            if (! $user->exists) {
                $user->password = Hash::make('password');
            }

            $user->save();

            // EMPLOYEE
            $employee = Employee::firstOrNew([
                'email' => $email,
            ]);

            // hanya saat employee baru
            if (! $employee->exists) {

                $nip = trim((string) ($row['nip'] ?? ''));

                $employee->nip = $nip !== '' && ! Employee::where('nip', $nip)->exists()
                    ? $nip
                    : $this->generateUniqueNip();

                $employee->phone = '';
                $employee->address = '';

                $employee->join_date = now()->format('Y-m-d');
            }

            $employee->user_id = $user->id;

            $employee->department_id = $department->id;
            $employee->position_id = $position->id;

            $employee->name = $name;
            $employee->email = $email;

            // update salary hanya jika kolom excel terisi
            if (
                isset($row['gaji_pokok']) &&
                $row['gaji_pokok'] !== ''
            ) {
                $employee->basic_salary = floatval($row['gaji_pokok']);
            }

            if (
                isset($row['tunjangan_jabatan']) &&
                $row['tunjangan_jabatan'] !== ''
            ) {
                $employee->allowance = floatval($row['tunjangan_jabatan']);
            }

            $employee->status = 'active';

            $employee->save();
        }
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
        } while (
            Employee::where('nip', $nip)->exists()
        );

        return $nip;
    }
}
