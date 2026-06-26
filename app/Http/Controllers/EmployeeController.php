<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Http\Requests\EmployeeImportRequest;
use App\Http\Requests\EmployeeRequest;
use App\Imports\EmployeeImport;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $employees = Employee::with(['department', 'position', 'user'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")

                        ->orWhereHas('department', function ($department) use ($search) {
                            $department->where('name', 'like', "%{$search}%");
                        })

                        ->orWhereHas('position', function ($position) use ($search) {
                            $position->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    public function export()
    {
        return Excel::download(new EmployeesExport(), 'employees.xlsx');
    }

    public function import()
    {
        return view('admin.employees.import');
    }

    public function importStore(EmployeeImportRequest $request)
    {
        Excel::import(new EmployeeImport(), $request->file('file'));

        return redirect()->route('admin.employees.index')
            ->with('status', 'Data karyawan berhasil diimpor.');
    }

    public function create()
    {
        return view('admin.employees.create', [
            'departments' => Department::orderBy('name')->get(),
            'positions' => Position::orderBy('name')->get(),
        ]);
    }

    public function store(EmployeeRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'employee',
        ]);

        Employee::create([
            'user_id' => $user->id,
            'department_id' => $data['department_id'],
            'position_id' => $data['position_id'],
            'nip' => $data['nip'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'join_date' => $data['join_date'],
            'basic_salary' => $data['basic_salary'],
            'allowance' => $data['allowance'],
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.employees.index')
            ->with('status', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        return redirect()->route('admin.employees.edit', $employee);
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', [
            'employee' => $employee,
            'departments' => Department::orderBy('name')->get(),
            'positions' => Position::orderBy('name')->get(),
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();

        $employee->update([
            'department_id' => $data['department_id'],
            'position_id' => $data['position_id'],
            'nip' => $data['nip'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'join_date' => $data['join_date'],
            'basic_salary' => $data['basic_salary'],
            'allowance' => $data['allowance'],
            'status' => $data['status'],
        ]);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $employee->user()->update($userData);

        return redirect()->route('admin.employees.index')
            ->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->user()->delete();
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('status', 'Karyawan berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ]);

        $deletedCount = DB::transaction(function () use ($data) {
            $employees = Employee::whereIn('id', $data['employee_ids'])->get(['id', 'user_id']);
            $userIds = $employees->pluck('user_id')->filter()->all();
            $employeeIds = $employees->pluck('id')->all();

            Employee::whereIn('id', $employeeIds)->delete();
            User::whereIn('id', $userIds)->delete();

            return count($employeeIds);
        });

        return redirect()->route('admin.employees.index')
            ->with('status', "{$deletedCount} karyawan berhasil dihapus.");
    }

    public function destroyAll()
    {
        try {
            $employeeCount = Employee::count();
            $payrollCount = DB::table('payrolls')->count();

            DB::beginTransaction();

            $userIds = Employee::whereNotNull('user_id')->pluck('user_id')->all();

            $deletedJobs = DB::table('jobs')
                ->where('payload', 'like', '%SendPayrollSlipEmailJob%')
                ->orWhere('payload', 'like', '%GeneratePayrollPdfJob%')
                ->delete();

            DB::table('failed_jobs')
                ->where('payload', 'like', '%SendPayrollSlipEmailJob%')
                ->orWhere('payload', 'like', '%GeneratePayrollPdfJob%')
                ->delete();

            Employee::query()->delete();
            User::whereIn('id', $userIds)->delete();

            DB::commit();

            Storage::disk('local')->deleteDirectory('payroll-slips');

            return redirect()->route('admin.employees.index')
                ->with(
                    'status',
                    "Semua karyawan berhasil dihapus ({$employeeCount} karyawan, {$payrollCount} payroll, {$deletedJobs} antrean payroll dibatalkan)."
                );
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return redirect()->route('admin.employees.index')
                ->with('error', 'Gagal menghapus semua karyawan: ' . $e->getMessage());
        }
    }
}
