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
            $totalEmployees = Employee::count();
            $totalPayrolls = Payroll::count();
            $lastPayrolls = Payroll::with('employee')->latest()->limit(5)->get();
            $monthlyPayroll = Payroll::selectRaw('period, SUM(total_salary) as total')
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->limit(6)
                ->get();

            return view('admin.dashboard', compact('totalEmployees', 'totalPayrolls', 'lastPayrolls', 'monthlyPayroll'));
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
}
