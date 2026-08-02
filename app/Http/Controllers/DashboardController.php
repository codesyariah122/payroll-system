<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $company = $user->company;
            $companyId = $user->company_id;

            $totalEmployees = Employee::where('company_id', $companyId)->count();
            $totalPayrolls = Payroll::where('company_id', $companyId)->count();
            $lastPayrolls = Payroll::with('employee')
                ->where('company_id', $companyId)
                ->latest()
                ->limit(5)
                ->get();
            $monthlyPayroll = Payroll::selectRaw('period, SUM(total_salary) as total')
                ->where('company_id', $companyId)
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->limit(6)
                ->get();
            $latestPeriod = $lastPayrolls->first()?->period;
            $latestPeriodPayrolls = Payroll::with(['employee.department', 'employee.position'])
                ->where('company_id', $companyId)
                ->when($latestPeriod, fn ($query) => $query->where('period', $latestPeriod))
                ->get();

            $payrollAnalysis = $this->payrollAnalysis($latestPeriodPayrolls);

            return view('admin.dashboard', compact('company', 'totalEmployees', 'totalPayrolls', 'lastPayrolls', 'monthlyPayroll', 'latestPeriod', 'payrollAnalysis'));
        }

        $employee = $user->employee;

        if (! $employee) {
            abort(403);
        }

        $payrolls = $employee->payrolls()->latest()->limit(5)->get();
        $lastPaid = $payrolls->first();
        $nextPayrollCount = $employee->payrolls()->count();

        return view('employee.dashboard', compact('employee', 'payrolls', 'nextPayrollCount', 'lastPaid'));
    }

    private function payrollAnalysis($payrolls): array
    {
        $totalPayrolls = $payrolls->count();
        $totalSalary = (float) $payrolls->sum('total_salary');
        $totalTargetWorkDays = (float) $payrolls->sum('target_work_days');
        $totalWorkDays = (float) $payrolls->sum('work_days');
        $totalOvertimeHours = (float) $payrolls->sum('overtime_hours') + (float) $payrolls->sum('special_overtime_hours');

        $salaryBands = [
            ['label' => 'Rp 0', 'count' => 0, 'color' => '#94a3b8'],
            ['label' => '< Rp 1 jt', 'count' => 0, 'color' => '#22d3ee'],
            ['label' => 'Rp 1-2 jt', 'count' => 0, 'color' => '#818cf8'],
            ['label' => 'Rp 2-3 jt', 'count' => 0, 'color' => '#a78bfa'],
            ['label' => '> Rp 3 jt', 'count' => 0, 'color' => '#34d399'],
        ];

        foreach ($payrolls as $payroll) {
            $salary = (float) $payroll->total_salary;

            if ($salary <= 0) {
                $salaryBands[0]['count']++;
            } elseif ($salary < 1000000) {
                $salaryBands[1]['count']++;
            } elseif ($salary < 2000000) {
                $salaryBands[2]['count']++;
            } elseif ($salary < 3000000) {
                $salaryBands[3]['count']++;
            } else {
                $salaryBands[4]['count']++;
            }
        }

        $workdayCompliance = [
            'under' => $payrolls->filter(fn ($payroll) => (float) $payroll->target_work_days > 0 && (float) $payroll->work_days < (float) $payroll->target_work_days)->count(),
            'on_target' => $payrolls->filter(fn ($payroll) => (float) $payroll->target_work_days > 0 && (float) $payroll->work_days === (float) $payroll->target_work_days)->count(),
            'over' => $payrolls->filter(fn ($payroll) => (float) $payroll->target_work_days > 0 && (float) $payroll->work_days > (float) $payroll->target_work_days)->count(),
        ];

        $departmentPayroll = $payrolls
            ->groupBy(fn ($payroll) => $payroll->department_name ?: $payroll->employee?->department?->name ?: 'Tanpa Departemen')
            ->map(fn ($items, $name) => [
                'name' => $name,
                'count' => $items->count(),
                'total' => (float) $items->sum('total_salary'),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $emailStatuses = $payrolls
            ->groupBy(fn ($payroll) => $payroll->email_status ?: 'pending')
            ->map(fn ($items, $status) => [
                'status' => $status,
                'label' => match ($status) {
                    'sent' => 'Terkirim',
                    'failed' => 'Gagal',
                    'skipped' => 'Dilewati',
                    'queued' => 'Queue',
                    default => 'Pending',
                },
                'count' => $items->count(),
            ])
            ->values();

        $topOvertimeEmployees = $payrolls
            ->map(fn ($payroll) => [
                'name' => $payroll->employee?->name ?? 'Tanpa Nama',
                'period' => $payroll->period,
                'hours' => (float) $payroll->overtime_hours + (float) $payroll->special_overtime_hours,
                'salary' => (float) $payroll->total_salary,
            ])
            ->sortByDesc('hours')
            ->take(5)
            ->values();

        return [
            'total_payrolls' => $totalPayrolls,
            'total_salary' => $totalSalary,
            'average_salary' => $totalPayrolls > 0 ? $totalSalary / $totalPayrolls : 0,
            'total_target_work_days' => $totalTargetWorkDays,
            'total_work_days' => $totalWorkDays,
            'workday_rate' => $totalTargetWorkDays > 0 ? min(100, round(($totalWorkDays / $totalTargetWorkDays) * 100, 1)) : 0,
            'total_overtime_hours' => $totalOvertimeHours,
            'average_overtime_hours' => $totalPayrolls > 0 ? round($totalOvertimeHours / $totalPayrolls, 1) : 0,
            'salary_bands' => $salaryBands,
            'workday_compliance' => $workdayCompliance,
            'department_payroll' => $departmentPayroll,
            'email_statuses' => $emailStatuses,
            'top_overtime_employees' => $topOvertimeEmployees,
        ];
    }
}
