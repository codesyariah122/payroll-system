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
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $companyId = $request->user()->company_id;

        $employees = Employee::with(['department', 'position', 'user'])
            ->where('company_id', $companyId)
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

    public function export(Request $request)
    {
        return Excel::download(new EmployeesExport($request->user()->company_id), 'employees.xlsx');
    }

    public function import()
    {
        return view('admin.employees.import');
    }

    public function importStore(EmployeeImportRequest $request)
    {
        $import = new EmployeeImport($request->user()->company);

        DB::transaction(function () use ($import, $request) {
            Excel::import($import, $request->file('file'));
        });

        $summary = $import->summary();

        $message = "Data karyawan berhasil diimpor: {$summary['imported']} data dibuat/diperbarui.";

        if ($summary['skipped'] > 0) {
            $message .= " {$summary['skipped']} baris dilewati karena email kosong.";
        }

        return redirect()->route('admin.employees.index')
            ->with('status', $message);
    }

    public function create(Request $request)
    {
        return view('admin.employees.create', [
            'departments' => Department::where('company_id', $request->user()->company_id)->orderBy('name')->get(),
            'positions' => Position::where('company_id', $request->user()->company_id)->orderBy('name')->get(),
        ]);
    }

    public function store(EmployeeRequest $request)
    {
        $data = $request->validated();
        $companyId = $request->user()->company_id;

        $this->validateEmployeeOrganization($data, $companyId);

        $user = null;

        if ($request->boolean('create_user')) {
            $user = User::create([
                'company_id' => $companyId,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'employee',
            ]);
        }

        Employee::create([
            'company_id' => $companyId,
            'user_id' => $user?->id,
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

    public function edit(Request $request, Employee $employee)
    {
        $this->authorizeCompany($request, $employee);

        return view('admin.employees.edit', [
            'employee' => $employee,
            'departments' => Department::where('company_id', $request->user()->company_id)->orderBy('name')->get(),
            'positions' => Position::where('company_id', $request->user()->company_id)->orderBy('name')->get(),
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        $this->authorizeCompany($request, $employee);

        $data = $request->validated();
        $this->validateEmployeeOrganization($data, $request->user()->company_id);

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

        if ($employee->user) {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (! empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $employee->user()->update($userData);
        } elseif ($request->boolean('create_user')) {
            $user = User::create([
                'company_id' => $request->user()->company_id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'employee',
            ]);

            $employee->update(['user_id' => $user->id]);
        }

        return redirect()->route('admin.employees.index')
            ->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Request $request, Employee $employee)
    {
        $this->authorizeCompany($request, $employee);

        $employee->user?->delete();
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

        $companyId = $request->user()->company_id;

        $deletedCount = DB::transaction(function () use ($data, $companyId) {
            $employees = Employee::where('company_id', $companyId)
                ->whereIn('id', $data['employee_ids'])
                ->get(['id', 'user_id']);
            $userIds = $employees->pluck('user_id')->filter()->all();
            $employeeIds = $employees->pluck('id')->all();

            Employee::whereIn('id', $employeeIds)->delete();
            User::whereIn('id', $userIds)->delete();

            return count($employeeIds);
        });

        return redirect()->route('admin.employees.index')
            ->with('status', "{$deletedCount} karyawan berhasil dihapus.");
    }

    public function destroyAll(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;
            $employeeIds = Employee::where('company_id', $companyId)->pluck('id')->all();
            $employeeCount = count($employeeIds);
            $payrollCount = DB::table('payrolls')->where('company_id', $companyId)->count();

            DB::beginTransaction();

            $userIds = Employee::where('company_id', $companyId)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->all();

            $deletedJobs = 0;

            Employee::whereIn('id', $employeeIds)->delete();
            User::whereIn('id', $userIds)->delete();

            DB::commit();

            Storage::disk('local')->deleteDirectory("payroll-slips/company-{$companyId}");

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

    private function authorizeCompany(Request $request, Employee $employee): void
    {
        abort_if($employee->company_id !== $request->user()->company_id, 404);
    }

    private function validateEmployeeOrganization(array $data, int $companyId): void
    {
        validator($data, [
            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')->where('company_id', $companyId),
            ],
            'position_id' => [
                'nullable',
                Rule::exists('positions', 'id')->where('company_id', $companyId),
            ],
        ])->validate();
    }
}
