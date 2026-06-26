<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Employee::with(['department', 'position'])
            ->get()
            ->map(function (Employee $employee) {
                return [
                    'NIP' => $employee->nip,
                    'Nama' => $employee->name,
                    'Email' => $employee->email,
                    'Departemen' => $employee->department?->name,
                    'Posisi' => $employee->position?->name,
                    'Telepon' => $employee->phone,
                    'Alamat' => $employee->address,
                    'Tanggal Masuk' => $employee->join_date?->format('Y-m-d'),
                    'Gaji Pokok' => $employee->basic_salary,
                    'Tunjangan' => $employee->allowance,
                    'Status' => ucfirst($employee->status),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama',
            'Email',
            'Departemen',
            'Posisi',
            'Telepon',
            'Alamat',
            'Tanggal Masuk',
            'Gaji Pokok',
            'Tunjangan',
            'Status',
        ];
    }
}
