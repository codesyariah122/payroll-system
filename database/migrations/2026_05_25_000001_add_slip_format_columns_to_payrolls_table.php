<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->double('target_work_days')->default(0)->after('period');
            $table->double('work_days')->default(0)->after('target_work_days');
            $table->double('overtime_hours')->default(0)->after('work_days');
            $table->double('special_overtime_hours')->default(0)->after('overtime_hours');
            $table->double('position_allowance')->default(0)->after('basic_salary');
            $table->double('attendance_allowance')->default(0)->after('position_allowance');
            $table->double('safety_incentive')->default(0)->after('attendance_allowance');
            $table->double('risk_allowance')->default(0)->after('safety_incentive');
            $table->double('placement_allowance')->default(0)->after('risk_allowance');
            $table->double('golden_shake_hand')->default(0)->after('placement_allowance');
            $table->double('tax_allowance')->default(0)->after('golden_shake_hand');
            $table->double('irregular_income')->default(0)->after('tax_allowance');
            $table->double('overtime_pay')->default(0)->after('irregular_income');
            $table->double('total_income')->default(0)->after('overtime_pay');
            $table->double('bpjamsostek')->default(0)->after('total_income');
            $table->double('bpjs_health')->default(0)->after('bpjamsostek');
            $table->double('attendance_deduction')->default(0)->after('bpjs_health');
            $table->double('fine')->default(0)->after('attendance_deduction');
            $table->double('employee_receivable')->default(0)->after('fine');
            $table->double('pph21_tax_object')->default(0)->after('employee_receivable');
            $table->double('total_deduction')->default(0)->after('pph21_tax_object');
            $table->double('take_home_pay')->default(0)->after('total_deduction');
        });

        DB::table('payrolls')->update([
            'position_allowance' => DB::raw('allowance'),
            'irregular_income' => DB::raw('bonus'),
            'overtime_pay' => DB::raw('overtime'),
            'total_income' => DB::raw('basic_salary + allowance + bonus + overtime'),
            'total_deduction' => DB::raw('deduction'),
            'take_home_pay' => DB::raw('total_salary'),
        ]);
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'target_work_days',
                'work_days',
                'overtime_hours',
                'special_overtime_hours',
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
            ]);
        });
    }
};
