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
        $period = $request->string('period')->trim()->toString();
        $companyId = $request->user()->company_id;

        $payrolls = Payroll::with([
            'employee.department',
            'employee.position'
        ])
            ->where('company_id', $companyId)
            ->when($period, fn ($query) => $query->where('period', $period))
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

        $periods = Payroll::where('company_id', $companyId)
            ->select('period')
            ->distinct()
            ->orderByDesc('period')
            ->pluck('period');

        return view('admin.payrolls.index', compact('payrolls', 'search', 'period', 'periods'));
    }

    public function create(Request $request)
    {
        return view('admin.payrolls.create', [
            'employees' => Employee::where('company_id', $request->user()->company_id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(PayrollRequest $request, PayrollService $payrollService)
    {
        $data = $request->validated();
        $employee = Employee::where('company_id', $request->user()->company_id)
            ->findOrFail($data['employee_id']);

        $payroll = $payrollService->createPayrollWithPdf($employee, $data);

        if ($request->boolean('send_email')) {
            SendPayrollSlipEmailJob::dispatch($payroll);
        }

        return redirect()->route('admin.payrolls.index')
            ->with('status', 'Payroll berhasil dibuat dan slip gaji disiapkan.');
    }

    public function show(Request $request, Payroll $payroll)
    {
        $this->authorizeCompany($request, $payroll);

        $payroll->load(['employee.department', 'employee.position']);

        return view('admin.payrolls.show', compact('payroll'));
    }

    public function edit(Request $request, Payroll $payroll)
    {
        $this->authorizeCompany($request, $payroll);

        return view('admin.payrolls.edit', [
            'payroll' => $payroll,
            'employees' => Employee::where('company_id', $request->user()->company_id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(PayrollRequest $request, Payroll $payroll, PayrollService $payrollService)
    {
        $this->authorizeCompany($request, $payroll);

        $data = $payrollService->normalizePayrollData($request->validated());
        Employee::where('company_id', $request->user()->company_id)->findOrFail($data['employee_id']);

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

    public function destroy(Request $request, Payroll $payroll)
    {
        $this->authorizeCompany($request, $payroll);

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

    public function export(Request $request)
    {
        return Excel::download(
            new PayrollExport($request->user()->company_id),
            'payroll-data.xlsx'
        );
    }

    public function importStore(PayrollImportRequest $request, PayrollService $payrollService)
    {
        $import = new PayrollImport($payrollService, $request->user()->company);

        Excel::import($import, $request->file('file'));

        $summary = $import->summary();

        if (! empty($summary['errors'])) {
            return back()
                ->with('error', 'Import payroll dibatalkan. ' . implode(' ', $summary['errors']));
        }

        $message = "Data payroll berhasil diimpor: {$summary['created']} data dibuat, {$summary['updated']} data diperbarui.";

        if ($summary['skipped'] > 0) {
            $message .= " {$summary['skipped']} baris dilewati karena email tidak cocok/kosong.";
        }

        return redirect()->route('admin.payrolls.index')
            ->with('status', $message);
    }

    public function sendBulk(Request $request)
    {
        $companyId = $request->user()->company_id;

        if ($request->boolean('send_all')) {

            $baseQuery = Payroll::with('employee')
                ->where('company_id', $companyId)
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

            $baseQuery = Payroll::with('employee')
                ->where('company_id', $companyId)
                ->whereIn('id', $ids);
        }

        $skippedZeroCount = (clone $baseQuery)
            ->where('take_home_pay', '<=', 0)
            ->count();

        if ($skippedZeroCount > 0) {
            (clone $baseQuery)
                ->where('take_home_pay', '<=', 0)
                ->update([
                    'email_status' => 'skipped',
                    'email_error' => 'Total gaji bersih 0, email tidak dikirim.',
                ]);
        }

        $query = (clone $baseQuery)
            ->where('take_home_pay', '>', 0);

        $payrollCount = $query->count();

        if ($payrollCount === 0) {
            $message = 'Data payroll tidak ditemukan untuk dikirim.';

            if ($skippedZeroCount > 0) {
                $message = "{$skippedZeroCount} payroll dilewati karena total gaji bersih 0. Tidak ada email yang dimasukkan ke antrean.";
            }

            return redirect()->route('admin.payrolls.index')
                ->with('status', $message);
        }

        if ($this->shouldUseLocalPayrollEmailTest()) {
            $testEmail = (string) config('payroll.local_email_test.recipient');

            if (! filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                return redirect()->route('admin.payrolls.index')
                    ->with('error', 'Email testing payroll lokal tidak valid.');
            }

            $samplePayroll = (clone $query)->first();

            $samplePayroll->update([
                'email_status' => 'queued',
                'email_error' => null,
                'email_sent_at' => null,
            ]);

            SendPayrollSlipEmailJob::dispatch($samplePayroll, $testEmail);

            (clone $query)
                ->where('id', '!=', $samplePayroll->id)
                ->update([
                    'email_status' => 'sent',
                    'email_error' => 'Local test mode: dianggap terkirim tanpa mengirim email.',
                    'email_sent_at' => now(),
                ]);

            $simulatedCount = max($payrollCount - 1, 0);
            $message = "Mode testing local aktif: 1 slip payroll dimasukkan ke antrean untuk {$testEmail}. {$simulatedCount} payroll lain ditandai terkirim tanpa kirim email.";

            if ($skippedZeroCount > 0) {
                $message .= " {$skippedZeroCount} payroll nominal 0 dilewati.";
            }

            return redirect()->route('admin.payrolls.index')
                ->with('status', $message);
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

        $message = "Email slip gaji untuk {$payrollCount} payroll telah dimasukkan ke antrean.";

        if ($skippedZeroCount > 0) {
            $message .= " {$skippedZeroCount} payroll nominal 0 dilewati.";
        }

        return redirect()->route('admin.payrolls.index')
            ->with('status', $message);
    }

    protected function shouldUseLocalPayrollEmailTest(): bool
    {
        return app()->environment('local')
            && (bool) config('payroll.local_email_test.enabled');
    }

    public function preview(Request $request, Payroll $payroll, PayrollService $payrollService)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $this->authorizeCompany($request, $payroll);
        } elseif ($user->employee?->id !== $payroll->employee_id) {
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

        if ($user->isAdmin()) {
            $this->authorizeCompany($request, $payroll);
        } elseif ($user->employee?->id !== $payroll->employee_id) {
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

    public function destroyAll(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;
            $data = $request->validate(['period' => ['required', 'string', 'max:255']]);
            $payrolls = Payroll::where('company_id', $companyId)
                ->where('period', $data['period'])
                ->get(['id', 'pdf_path']);
            $payrollCount = $payrolls->count();

            if ($payrollCount === 0) {
                return redirect()->route('admin.payrolls.index')
                    ->with('error', 'Periode payroll tidak ditemukan.');
            }

            DB::beginTransaction();

            $deletedJobs = 0;

            Payroll::whereIn('id', $payrolls->pluck('id'))->delete();

            DB::commit();

            foreach ($payrolls->pluck('pdf_path')->filter() as $pdfPath) {
                Storage::disk('local')->delete($pdfPath);
            }

            return redirect()
                ->route('admin.payrolls.index')
                ->with(
                    'status',
                    "Payroll periode {$data['period']} berhasil dihapus ({$payrollCount} data). Riwayat periode lain tetap tersimpan."
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

    private function authorizeCompany(Request $request, Payroll $payroll): void
    {
        abort_if($payroll->company_id !== $request->user()->company_id, 404);
    }
}
