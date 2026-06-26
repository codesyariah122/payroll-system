@php
    $slipFields = [
        'target_work_days' => 'Target HK',
        'work_days' => 'HK',
        'overtime_hours' => 'Jam Lembur',
        'special_overtime_hours' => 'Jam Lembur Khusus',
        'basic_salary' => 'Gaji Pokok',
        'position_allowance' => 'Tunjangan Jabatan',
        'attendance_allowance' => 'Tunjangan Kehadiran',
        'safety_incentive' => 'Insentif Keselamatan',
        'risk_allowance' => 'Tunjangan Risiko',
        'placement_allowance' => 'Tunjangan Penempatan',
        'golden_shake_hand' => 'Golden Shake Hand',
        'tax_allowance' => 'Tunjangan PPh',
        'irregular_income' => 'Bonus/THR/Penghasilan Tidak Teratur',
        'overtime_pay' => 'Uang Lembur',
        'total_income' => 'Jumlah Pendapatan',
        'bpjamsostek' => 'BPJamsostek',
        'bpjs_health' => 'BPJS Kesehatan',
        'attendance_deduction' => 'Potongan Kehadiran (Absensi/Prorate)',
        'fine' => 'Denda',
        'employee_receivable' => 'Piutang Karyawan',
        'pph21_tax_object' => 'Objek Pajak PPh 21',
        'total_deduction' => 'Jumlah Potongan',
        'take_home_pay' => 'THP/Gaji Bersih',
    ];
@endphp

@foreach ($slipFields as $field => $label)
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label }}</label>
        <input type="number" step="0.01" name="{{ $field }}"
            value="{{ old($field, isset($payroll) ? $payroll->{$field} : null) }}"
            class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            {{ $field === 'basic_salary' ? 'required' : '' }}>
        @error($field)
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
@endforeach
