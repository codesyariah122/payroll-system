<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollTemplate;
use App\Models\Position;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PayrollTemplateController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $templates = PayrollTemplate::where('company_id', $companyId)->latest()->paginate(15);
        $activeTemplate = PayrollTemplate::where('company_id', $companyId)
            ->where('type', 'html')
            ->where('is_active', true)
            ->first();

        return view('admin.payroll-templates.index', compact('templates', 'activeTemplate'));
    }

    public function create()
    {
        return view('admin.payroll-templates.create', [
            'sampleHtml' => $this->sampleHtml(),
        ]);
    }

    public function previewDefault()
    {
        $payroll = $this->samplePayroll();

        return Pdf::loadView('payrolls.slip', [
            'payroll' => $payroll,
        ])
            ->setPaper('a4', 'portrait')
            ->stream('preview-template-default-payroll.pdf');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:html,pdf'],
            'html_content' => ['nullable', 'required_if:type,html', 'string'],
            'pdf_file' => ['nullable', 'required_if:type,pdf', 'file', 'mimes:pdf', 'max:5120'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $data) {
            $path = null;
            $companyId = $request->user()->company_id;

            if ($request->hasFile('pdf_file')) {
                $path = $request->file('pdf_file')->store('payroll-template-references', 'local');
            }

            if ($data['type'] === 'html' && $request->boolean('is_active')) {
                PayrollTemplate::where('company_id', $companyId)
                    ->where('type', 'html')
                    ->update(['is_active' => false]);
            }

            PayrollTemplate::create([
                'company_id' => $companyId,
                'name' => $data['name'],
                'type' => $data['type'],
                'html_content' => $data['type'] === 'html' ? $data['html_content'] : null,
                'pdf_path' => $path,
                'is_active' => $data['type'] === 'html' && $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.payroll-templates.index')
            ->with('status', 'Template payroll berhasil dibuat.');
    }

    public function edit(Request $request, PayrollTemplate $payrollTemplate)
    {
        $this->authorizeCompany($request, $payrollTemplate);

        return view('admin.payroll-templates.edit', [
            'template' => $payrollTemplate,
            'sampleHtml' => $this->sampleHtml(),
        ]);
    }

    public function update(Request $request, PayrollTemplate $payrollTemplate)
    {
        $this->authorizeCompany($request, $payrollTemplate);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'html_content' => ['nullable', 'required_if:type,html', 'string'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $payrollTemplate, $data) {
            if ($payrollTemplate->isHtml() && $request->boolean('is_active')) {
                PayrollTemplate::where('company_id', $request->user()->company_id)
                    ->where('type', 'html')
                    ->whereKeyNot($payrollTemplate->id)
                    ->update(['is_active' => false]);
            }

            if ($request->hasFile('pdf_file')) {
                if ($payrollTemplate->pdf_path) {
                    Storage::disk('local')->delete($payrollTemplate->pdf_path);
                }

                $payrollTemplate->pdf_path = $request->file('pdf_file')
                    ->store('payroll-template-references', 'local');
            }

            $payrollTemplate->name = $data['name'];

            if ($payrollTemplate->isHtml()) {
                $payrollTemplate->html_content = $data['html_content'];
                $payrollTemplate->is_active = $request->boolean('is_active');
            }

            $payrollTemplate->save();
        });

        return redirect()->route('admin.payroll-templates.index')
            ->with('status', 'Template payroll berhasil diperbarui.');
    }

    public function activate(Request $request, PayrollTemplate $payrollTemplate)
    {
        $this->authorizeCompany($request, $payrollTemplate);

        if (! $payrollTemplate->isHtml()) {
            return redirect()->route('admin.payroll-templates.index')
                ->with('error', 'File PDF upload hanya untuk referensi. Gunakan template HTML untuk generate slip otomatis.');
        }

        DB::transaction(function () use ($request, $payrollTemplate) {
            PayrollTemplate::where('company_id', $request->user()->company_id)
                ->where('type', 'html')
                ->update(['is_active' => false]);

            $payrollTemplate->update(['is_active' => true]);
        });

        return redirect()->route('admin.payroll-templates.index')
            ->with('status', 'Template payroll berhasil diaktifkan.');
    }

    public function deactivate(Request $request)
    {
        PayrollTemplate::where('company_id', $request->user()->company_id)
            ->where('type', 'html')
            ->update(['is_active' => false]);

        return redirect()->route('admin.payroll-templates.index')
            ->with('status', 'Template default bawaan berhasil diaktifkan.');
    }

    public function download(Request $request, PayrollTemplate $payrollTemplate)
    {
        $this->authorizeCompany($request, $payrollTemplate);

        abort_unless($payrollTemplate->pdf_path && Storage::disk('local')->exists($payrollTemplate->pdf_path), 404);

        return response()->download(
            Storage::disk('local')->path($payrollTemplate->pdf_path),
            $payrollTemplate->name . '.pdf'
        );
    }

    public function destroy(Request $request, PayrollTemplate $payrollTemplate)
    {
        $this->authorizeCompany($request, $payrollTemplate);

        if ($payrollTemplate->pdf_path) {
            Storage::disk('local')->delete($payrollTemplate->pdf_path);
        }

        $payrollTemplate->delete();

        return redirect()->route('admin.payroll-templates.index')
            ->with('status', 'Template payroll berhasil dihapus.');
    }

    private function authorizeCompany(Request $request, PayrollTemplate $payrollTemplate): void
    {
        abort_if($payrollTemplate->company_id !== $request->user()->company_id, 404);
    }

    protected function sampleHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; margin: 24px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #111827; padding: 7px; }
        .header td { border: none; }
        .title { font-size: 22px; font-weight: bold; text-align: right; }
        .company { font-size: 16px; font-weight: bold; }
        .money { text-align: right; white-space: nowrap; }
        .section { margin-top: 14px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <div class="company">PT. CITARASA KULINER INDONESIA</div>
                <div>Periode: [[period]]</div>
            </td>
            <td class="title">SLIP GAJI</td>
        </tr>
    </table>

    <table class="section">
        <tr><td>Nama</td><td>[[employee_name]]</td><td>Target HK</td><td>[[target_work_days]]</td></tr>
        <tr><td>Jabatan</td><td>[[position_name]]</td><td>HK</td><td>[[work_days]]</td></tr>
        <tr><td>Departemen</td><td>[[department_name]]</td><td>Jam Lembur</td><td>[[overtime_hours]]</td></tr>
    </table>

    <table class="section">
        <tr><th>Pendapatan</th><th>Nominal</th><th>Potongan</th><th>Nominal</th></tr>
        <tr><td>Gaji Pokok</td><td class="money">[[basic_salary]]</td><td>BPJS Jamsostek</td><td class="money">[[bpjamsostek]]</td></tr>
        <tr><td>Tunjangan Jabatan</td><td class="money">[[position_allowance]]</td><td>BPJS Kesehatan</td><td class="money">[[bpjs_health]]</td></tr>
        <tr><td>Tunjangan Kehadiran</td><td class="money">[[attendance_allowance]]</td><td>Potongan Kehadiran</td><td class="money">[[attendance_deduction]]</td></tr>
        <tr><td>Uang Lembur</td><td class="money">[[overtime_pay]]</td><td>Denda</td><td class="money">[[fine]]</td></tr>
    </table>

    <table class="section">
        <tr><td>Total Pendapatan</td><td class="money">[[total_income]]</td><td>Total Potongan</td><td class="money">[[total_deduction]]</td></tr>
        <tr><td colspan="2"><strong>Gaji Bersih</strong></td><td colspan="2" class="money"><strong>[[take_home_pay]]</strong></td></tr>
    </table>
</body>
</html>
HTML;
    }

    protected function samplePayroll(): Payroll
    {
        $department = new Department([
            'name' => 'Supply Chain Management',
        ]);

        $position = new Position([
            'name' => 'Marketplace Operation Operator',
        ]);

        $employee = new Employee([
            'nip' => 'EMP-0001',
            'name' => 'Contoh Karyawan',
            'email' => 'karyawan@example.com',
        ]);

        $employee->setRelation('department', $department);
        $employee->setRelation('position', $position);

        $payroll = new Payroll([
            'period' => '21 Mei 2026 - 20 Juni 2026',
            'position_name' => 'Marketplace Operation Operator',
            'department_name' => 'Supply Chain Management',
            'target_work_days' => 21,
            'work_days' => 21,
            'overtime_hours' => 0,
            'special_overtime_hours' => 0,
            'basic_salary' => 2587500,
            'position_allowance' => 0,
            'attendance_allowance' => 500000,
            'safety_incentive' => 0,
            'risk_allowance' => 0,
            'placement_allowance' => 0,
            'golden_shake_hand' => 0,
            'tax_allowance' => 0,
            'irregular_income' => 0,
            'overtime_pay' => 468638,
            'total_income' => 3556138,
            'bpjamsostek' => 51750,
            'bpjs_health' => 47377,
            'attendance_deduction' => 0,
            'fine' => 0,
            'employee_receivable' => 0,
            'pph21_tax_object' => 0,
            'total_deduction' => 99127,
            'take_home_pay' => 3457011,
            'allowance' => 500000,
            'bonus' => 0,
            'overtime' => 468638,
            'deduction' => 99127,
            'total_salary' => 3457011,
        ]);

        $payroll->setRelation('employee', $employee);

        return $payroll;
    }
}
