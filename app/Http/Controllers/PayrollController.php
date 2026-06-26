<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollImportRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PayrollRequest;
use Illuminate\Support\Facades\Log;
use App\Imports\PayrollImport;
use App\Jobs\SendPayrollSlipEmailJob;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollService;
use App\Exports\PayrollExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $payrolls = Payroll::with([
            'employee.department',
            'employee.position'
        ])
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('period', 'like', "%{$search}%")
                        ->orWhere('email_status', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($employee) use ($search) {

                            $employee->where('name', 'like', "%{$search}%")
                                ->orWhere('nip', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByRaw("
        CASE
            WHEN email_status = 'sending' THEN 1
            WHEN email_status = 'queued' THEN 1
            WHEN email_status = 'failed' THEN 2
            WHEN email_status = 'pending' THEN 3
            WHEN email_status = 'sent' THEN 4
            ELSE 5
        END
    ")
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payrolls.index', compact(
            'payrolls',
            'search'
        ));
    }

    public function create()
    {
        return view('admin.payrolls.create', [
            'employees' => Employee::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(PayrollRequest $request, PayrollService $payrollService)
    {
        $data = $request->validated();
        $employee = Employee::findOrFail($data['employee_id']);

        $payroll = $payrollService->createPayrollWithPdf($employee, $data);

        if ($request->boolean('send_email')) {
            SendPayrollSlipEmailJob::dispatch($payroll);
        }

        return redirect()->route('admin.payrolls.index')
            ->with('status', 'Payroll berhasil dibuat dan slip gaji disiapkan.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['employee.department', 'employee.position']);

        return view('admin.payrolls.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        return view('admin.payrolls.edit', [
            'payroll' => $payroll,
            'employees' => Employee::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(PayrollRequest $request, Payroll $payroll, PayrollService $payrollService)
    {
        $data = $payrollService->normalizePayrollData($request->validated());

        $payroll->update([
            'employee_id' => $data['employee_id'],
            'period' => $data['period'],
            'target_work_days' => $data['target_work_days'],
            'work_days' => $data['work_days'],
            'overtime_hours' => $data['overtime_hours'],
            'special_overtime_hours' => $data['special_overtime_hours'],
            'basic_salary' => $data['basic_salary'],
            'position_allowance' => $data['position_allowance'],
            'attendance_allowance' => $data['attendance_allowance'],
            'safety_incentive' => $data['safety_incentive'],
            'risk_allowance' => $data['risk_allowance'],
            'placement_allowance' => $data['placement_allowance'],
            'golden_shake_hand' => $data['golden_shake_hand'],
            'tax_allowance' => $data['tax_allowance'],
            'irregular_income' => $data['irregular_income'],
            'overtime_pay' => $data['overtime_pay'],
            'total_income' => $data['total_income'],
            'bpjamsostek' => $data['bpjamsostek'],
            'bpjs_health' => $data['bpjs_health'],
            'attendance_deduction' => $data['attendance_deduction'],
            'fine' => $data['fine'],
            'employee_receivable' => $data['employee_receivable'],
            'pph21_tax_object' => $data['pph21_tax_object'],
            'total_deduction' => $data['total_deduction'],
            'take_home_pay' => $data['take_home_pay'],
            'allowance' => $data['allowance'],
            'bonus' => $data['bonus'],
            'overtime' => $data['overtime'],
            'deduction' => $data['deduction'],
            'total_salary' => $data['total_salary'],
        ]);

        $payroll->pdf_path = $payrollService->getPdfService()->generate($payroll);
        $payroll->save();

        if ($request->boolean('send_email')) {
            SendPayrollSlipEmailJob::dispatch($payroll);
        }

        return redirect()->route('admin.payrolls.index')
            ->with('status', 'Payroll berhasil diperbarui.');
    }

    public function destroy(Payroll $payroll)
    {
        try {

            // hapus file pdf jika ada
            if (!empty($payroll->pdf_path)) {

                if (Storage::disk('local')->exists($payroll->pdf_path)) {
                    Storage::disk('local')->delete($payroll->pdf_path);
                }
            }

            $payroll->delete();

            return redirect()
                ->route('admin.payrolls.index')
                ->with('status', 'Payroll berhasil dihapus.');
        } catch (\Throwable $e) {

            Log::error('DELETE PAYROLL ERROR', [
                'message' => $e->getMessage(),
                'payroll_id' => $payroll->id,
            ]);

            return redirect()
                ->route('admin.payrolls.index')
                ->with('error', 'Gagal menghapus payroll: ' . $e->getMessage());
        }
    }

    public function import()
    {
        return view('admin.payrolls.import');
    }

    public function export()
    {
        return Excel::download(
            new PayrollExport,
            'payroll-data.xlsx'
        );
    }

    public function importStore(PayrollImportRequest $request, PayrollService $payrollService)
    {
        Excel::import(new PayrollImport($payrollService), $request->file('file'));

        return redirect()->route('admin.payrolls.index')
            ->with('status', 'Data payroll berhasil diimpor.');
    }

    public function sendBulk(Request $request)
    {
        if ($request->boolean('send_all')) {

            $query = Payroll::with('employee')
                ->where(function ($q) {
                    $q->whereNull('email_status')
                        ->orWhereIn('email_status', [
                            'pending',
                            'failed',
                        ]);
                });
        } else {

            $ids = $request->input('payroll_ids', []);

            if (empty($ids)) {

                return redirect()->route('admin.payrolls.index')
                    ->with('status', 'Silakan pilih minimal satu payroll untuk dikirim.');
            }

            $query = Payroll::with('employee')
                ->whereIn('id', $ids)
                ->where(function ($q) {
                    $q->whereNull('email_status')
                        ->orWhereIn('email_status', [
                            'pending',
                            'failed',
                        ]);
                });
        }

        $payrollCount = $query->count();

        if ($payrollCount === 0) {

            return redirect()->route('admin.payrolls.index')
                ->with('status', 'Data payroll tidak ditemukan untuk dikirim.');
        }

        $delay = 0;

        $query->chunkById(100, function ($payrolls) use (&$delay) {

            foreach ($payrolls as $payroll) {

                if (! filter_var($payroll->employee->email, FILTER_VALIDATE_EMAIL)) {

                    $payroll->update([
                        'email_status' => 'failed',
                        'email_error' => 'Invalid email format',
                    ]);

                    continue;
                }

                $payroll->update([
                    'email_status' => 'queued',
                    'email_error' => null,
                    'email_sent_at' => null,
                ]);

                SendPayrollSlipEmailJob::dispatch($payroll)
                    ->delay(now()->addSeconds($delay));

                $delay += 60;
            }
        });

        return redirect()->route('admin.payrolls.index')
            ->with(
                'status',
                "Email slip gaji untuk {$payrollCount} payroll telah dimasukkan ke antrean."
            );
    }

    public function preview(Request $request, Payroll $payroll, PayrollService $payrollService)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $user->employee?->id !== $payroll->employee_id) {
            abort(403);
        }

        $payroll->pdf_path = $payrollService
            ->getPdfService()
            ->generate($payroll);

        $payroll->save();

        return response()->file(
            Storage::disk('local')->path($payroll->pdf_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline',
            ]
        );
    }

    public function employeeIndex(Request $request)
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            abort(403);
        }

        $payrolls = $employee->payrolls()->with('employee.position', 'employee.department')->latest()->paginate(15);

        return view('employee.payrolls.index', compact('payrolls'));
    }

    public function employeeShow(Request $request, Payroll $payroll)
    {
        if ($request->user()->employee?->id !== $payroll->employee_id) {
            abort(403);
        }

        $payroll->load(['employee.department', 'employee.position']);

        return view('employee.payrolls.show', compact('payroll'));
    }

    public function download(Request $request, Payroll $payroll, PayrollService $payrollService)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $user->employee?->id !== $payroll->employee_id) {
            abort(403);
        }

        $payroll->pdf_path = $payrollService->getPdfService()->generate($payroll);
        $payroll->save();

        return response()->download(
            storage_path('app/' . $payroll->pdf_path),
            sprintf(
                'slip-gaji-%s-%s.pdf',
                $payroll->employee->nip,
                str_replace('/', '-', $payroll->period)
            )
        );
    }

    public function destroyAll()
    {
        try {
            $payrollCount = Payroll::count();

            DB::beginTransaction();

            $deletedJobs = DB::table('jobs')
                ->where('payload', 'like', '%SendPayrollSlipEmailJob%')
                ->orWhere('payload', 'like', '%GeneratePayrollPdfJob%')
                ->delete();

            DB::table('failed_jobs')
                ->where('payload', 'like', '%SendPayrollSlipEmailJob%')
                ->orWhere('payload', 'like', '%GeneratePayrollPdfJob%')
                ->delete();

            Payroll::query()->delete();

            DB::commit();

            Storage::disk('local')->deleteDirectory('payroll-slips');

            return redirect()
                ->route('admin.payrolls.index')
                ->with(
                    'status',
                    "Semua payroll berhasil dihapus ({$payrollCount} data, {$deletedJobs} antrean payroll dibatalkan)."
                );
        } catch (\Throwable $e) {

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('DELETE ALL PAYROLL ERROR', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.payrolls.index')
                ->with('error', $e->getMessage());
        }
    }
}
