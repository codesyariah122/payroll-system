<div
    style="font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #111827;">
    <div style="max-width: 680px; margin: 0 auto; padding: 24px; background: #f8fafc;">
        <div
            style="background: #ffffff; border-radius: 16px; padding: 24px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);">
            <h1 style="font-size: 22px; margin-bottom: 12px; color: #111827;">Slip Gaji Anda Sudah Siap</h1>
            <p style="color: #475569; line-height: 1.7;">Hai {{ $payroll->employee->name }},</p>
            <p style="color: #475569; line-height: 1.7;">Slip gaji untuk periode <strong>{{ $payroll->period }}</strong>
                telah dibuat dan dilampirkan pada email ini. Mohon simpan dokumen ini sebagai bukti pembayaran gaji
                Anda.</p>
            <div style="background: #f1f5f9; border-radius: 12px; padding: 16px; margin-top: 20px;">
                <p style="margin: 0; color: #0f172a;"><strong>Nama:</strong> {{ $payroll->employee->name }}</p>
                {{-- <p style="margin: 0; color: #0f172a;"><strong>NIP:</strong> {{ $payroll->employee->nip }}</p> --}}
                <p style="margin: 0; color: #0f172a;"><strong>Periode:</strong> {{ $payroll->period }}</p>
            </div>
            <p style="color: #475569; line-height: 1.7; margin-top: 20px;">Slip gaji bersifat rahasia dan hanya untuk
                email yang dituju
            </p>
            <p style="color: #475569; line-height: 1.7;">Terima kasih,</p>
            <p style="color: #475569; line-height: 1.7;"><strong>Team HC Operation</strong></p>
        </div>
    </div>
</div>
