@php
    $details = [
        'Target HK' => $payroll->target_work_days,
        'HK' => $payroll->work_days,
        'Jam Lembur' => $payroll->overtime_hours,
        'Jam Lembur Khusus' => $payroll->special_overtime_hours,
        'Gaji Pokok' => $payroll->basic_salary,
        'Tunjangan Jabatan' => $payroll->position_allowance,
        'Tunjangan Kehadiran' => $payroll->attendance_allowance,
        'Insentif Keselamatan' => $payroll->safety_incentive,
        'Tunjangan Risiko' => $payroll->risk_allowance,
        'Tunjangan Penempatan' => $payroll->placement_allowance,
        'Golden Shake Hand' => $payroll->golden_shake_hand,
        'Tunjangan PPh' => $payroll->tax_allowance,
        'Bonus/THR/Penghasilan Tidak Teratur' => $payroll->irregular_income,
        'Uang Lembur' => $payroll->overtime_pay,
        'Jumlah Pendapatan' => $payroll->total_income,
        'BPJS Jamsostek (JHT)' => $payroll->bpjamsostek,
        'BPJS Kesehatan' => $payroll->bpjs_health,
        'Potongan Kehadiran (Absensi/Prorate)' => $payroll->attendance_deduction,
        'Denda' => $payroll->fine,
        'Piutang Karyawan' => $payroll->employee_receivable,
        'Objek Pajak PPh 21' => $payroll->pph21_tax_object,
        'Jumlah Potongan' => $payroll->total_deduction,
        'THP/Gaji Bersih' => $payroll->take_home_pay,
    ];

    $plainFields = ['Target HK', 'HK', 'Jam Lembur', 'Jam Lembur Khusus'];
@endphp

<dl class="grid gap-4 sm:grid-cols-2">

    @foreach ($details as $label => $value)
        <div>

            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                {{ $label }}
            </dt>

            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">

                @if (in_array($label, $plainFields))
                    {{ (int) $value }}
                @else
                    Rp {{ number_format($value, 0, ',', '.') }}
                @endif

            </dd>

        </div>
    @endforeach

</dl>
