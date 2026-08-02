<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::where('company_id', $request->user()->company_id)
            ->latest()
            ->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(DepartmentRequest $request)
    {
        Department::create($request->validated() + [
            'company_id' => $request->user()->company_id,
        ]);

        return redirect()->route('admin.departments.index')
            ->with('status', 'Department berhasil dibuat.');
    }

    public function show(Department $department)
    {
        return redirect()->route('admin.departments.edit', $department);
    }

    public function edit(Request $request, Department $department)
    {
        $this->authorizeCompany($request, $department);

        return view('admin.departments.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        $this->authorizeCompany($request, $department);

        $department->update($request->validated());

        return redirect()->route('admin.departments.index')
            ->with('status', 'Department berhasil diperbarui.');
    }

    public function destroy(Request $request, Department $department)
    {
        $this->authorizeCompany($request, $department);

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('status', 'Department berhasil dihapus.');
    }

    private function authorizeCompany(Request $request, Department $department): void
    {
        abort_if($department->company_id !== $request->user()->company_id, 404);
    }
}
