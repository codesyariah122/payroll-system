<?php

namespace App\Support;

use App\Models\Payroll;

class PayrollSlipFormat
{
    public const COLUMNS = [
        'No' => null,
        'Bulan' => 'period',
        'Nama' => null,
        'Email' => null,
        'Jabatan' => null,
        'Departemen' => null,
        'Target HK' => 'target_work_days',
        'HK' => 'work_days',
        'Jam Lembur' => 'overtime_hours',
        'Jam Lembur Khusus' => 'special_overtime_hours',
        'Gaji Pokok' => 'basic_salary',
        'Tunjangan Jabatan' => 'position_allowance',
        'Tunjangan Kehadiran' => 'attendance_allowance',
        'Insentif Keselamatan' => 'safety_incentive',
        'Tunjangan Risiko' => 'risk_allowance',
        'Tunjangan Penempatan' => 'placement_allowance',
        'Golden Shake Hand' => 'golden_shake_hand',
        'Tunjangan PPh' => 'tax_allowance',
        'Bonus/THR/Penghasilan Tidak teratur' => 'irregular_income',
        'Uang Lembur' => 'overtime_pay',
        'Jumlah Pendapatan' => 'total_income',
        'BPJamsostek' => 'bpjamsostek',
        'BPJS Kesehatan' => 'bpjs_health',
        'Potongan Kehadiran (Absensi/Prorate)' => 'attendance_deduction',
        'Denda' => 'fine',
        'Piutang Karyawan' => 'employee_receivable',
        'Objek Pajak PPh 21' => 'pph21_tax_object',
        'Jumlah Potongan' => 'total_deduction',
        'THP/Gaji Bersih' => 'take_home_pay',
    ];

    public const NUMERIC_FIELDS = [
        'target_work_days',
        'work_days',
        'overtime_hours',
        'special_overtime_hours',
        'basic_salary',
        'position_allowance',
        'attendance_allowance',
        'safety_incentive',
        'risk_allowance',
        'placement_allowance',
        'golden_shake_hand',
        'tax_allowance',
        'irregular_income',
        'overtime_pay',
        'total_income',
        'bpjamsostek',
        'bpjs_health',
        'attendance_deduction',
        'fine',
        'employee_receivable',
        'pph21_tax_object',
        'total_deduction',
        'take_home_pay',
    ];

    public const EARNING_FIELDS = [
        'basic_salary',
        'position_allowance',
        'attendance_allowance',
        'safety_incentive',
        'risk_allowance',
        'placement_allowance',
        'golden_shake_hand',
        'tax_allowance',
        'irregular_income',
        'overtime_pay',
    ];

    public const DEDUCTION_FIELDS = [
        'bpjamsostek',
        'bpjs_health',
        'attendance_deduction',
        'fine',
        'employee_receivable',
        'pph21_tax_object',
    ];

    public const IMPORT_ALIASES = [
        'period' => ['bulan', 'period', 'periode'],
        'target_work_days' => ['target_hk', 'target_work_days'],
        'work_days' => ['hk', 'work_days'],
        'overtime_hours' => ['jam_lembur', 'overtime_hours'],
        'special_overtime_hours' => ['jam_lembur_khusus', 'special_overtime_hours'],
        'basic_salary' => ['gaji_pokok', 'basic_salary'],
        'position_allowance' => ['tunjangan_jabatan', 'position_allowance'],
        'attendance_allowance' => ['tunjangan_kehadiran', 'attendance_allowance'],
        'safety_incentive' => ['insentif_keselamatan', 'safety_incentive'],
        'risk_allowance' => ['tunjangan_risiko', 'risk_allowance'],
        'placement_allowance' => ['tunjangan_penempatan', 'placement_allowance'],
        'golden_shake_hand' => ['golden_shake_hand'],
        'tax_allowance' => ['tunjangan_pph', 'tax_allowance'],
        'irregular_income' => ['bonus_thr_penghasilan_tidak_teratur', 'bonus', 'irregular_income'],
        'overtime_pay' => ['uang_lembur', 'overtime', 'overtime_pay'],
        'total_income' => ['jumlah_pendapatan', 'total_income'],
        'bpjamsostek' => ['bpjamsostek'],
        'bpjs_health' => ['bpjs_kesehatan', 'bpjs_health'],
        'attendance_deduction' => ['potongan_kehadiran_absensiprorate', 'potongan_kehadiran_absensi_prorate', 'attendance_deduction'],
        'fine' => ['denda', 'fine'],
        'employee_receivable' => ['piutang_karyawan', 'employee_receivable'],
        'pph21_tax_object' => ['objek_pajak_pph_21', 'objek_pajak_pph21', 'pph21_tax_object'],
        'total_deduction' => ['jumlah_potongan', 'deduction', 'total_deduction'],
        'take_home_pay' => ['thpgaji_bersih', 'thp_gaji_bersih', 'total_salary', 'take_home_pay'],
    ];

    public static function headings(): array
    {
        return array_keys(self::COLUMNS);
    }

    public static function exportRow(Payroll $payroll, int $number): array
    {
        $employee = $payroll->employee;

        return [
            $number,
            $payroll->period,
            $employee?->name,
            $employee?->email,
            $payroll->position_name ?: $employee?->position?->name,
            $payroll->department_name ?: $employee?->department?->name,
            $payroll->target_work_days,
            $payroll->work_days,
            $payroll->overtime_hours,
            $payroll->special_overtime_hours,
            $payroll->basic_salary,
            $payroll->position_allowance,
            $payroll->attendance_allowance,
            $payroll->safety_incentive,
            $payroll->risk_allowance,
            $payroll->placement_allowance,
            $payroll->golden_shake_hand,
            $payroll->tax_allowance,
            $payroll->irregular_income,
            $payroll->overtime_pay,
            $payroll->total_income,
            $payroll->bpjamsostek,
            $payroll->bpjs_health,
            $payroll->attendance_deduction,
            $payroll->fine,
            $payroll->employee_receivable,
            $payroll->pph21_tax_object,
            $payroll->total_deduction,
            $payroll->take_home_pay,
        ];
    }
}
