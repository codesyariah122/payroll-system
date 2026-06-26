<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji {{ $payroll->period }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 14px 22px;
            line-height: 1.35;
        }

        table {
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .info {
            width: 100%;
            margin-top: 5px;
        }

        .info td {
            padding: 5px;
            border: 1px solid #000;
            font-size: 10px;
        }

        .info-label {
            font-weight: bold;
            width: 170px;
        }

        .money {
            text-align: right;
            white-space: nowrap;
        }



        .content-table {
            width: 100%;
        }

        .content-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 10px;
        }

        .total-table {
            width: 100%;
            margin-top: 14px;
        }

        .total-table td {
            border: 1px solid #000;
            padding: 6px;
            font-weight: bold;
            font-size: 10px;
        }

        .net-salary {
            width: 100%;
            margin-top: 10px;
        }

        .net-salary td {
            border: 1px solid #000;
            padding: 7px;
            font-size: 12px;
            font-weight: bold;
        }

        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #444;
        }

        /* ======================
       HEADER
    ====================== */

        .header-table {
            width: 100%;
        }

        .header-table td {
            border: none;
        }

        .logo-col {
            width: 80px;
        }

        .logo {
            width: 65px;
            margin-top: 2px;
        }

        .company-col {
            text-align: left;
            padding-left: 6px;
        }

        .company-name {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-address {
            font-size: 10px;
            line-height: 1.5;
        }

        .slip-col {
            width: 160px;
            text-align: right;
        }

        .slip-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        .slip-period {
            font-size: 11px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <!-- =========================
     HEADER
========================= -->

    <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:6px;">

        <tr>

            <!-- LOGO -->
            <td width="72" valign="top">

                <img src="{{ asset('build/assets/logo-crk.jpeg') }}" style="width:58px; margin-top:2px;">

            </td>

            <!-- COMPANY -->
            <td valign="top" style="padding-left:4px;">

                <div
                    style="
    font-size:14px;
    letter-spacing:0.1px;
    font-weight:bold;
    line-height:1.2;
    margin-top:0px;
">
                    PT. CITARASA KULINER<br>
                    INDONESIA
                </div>

                <div style="
    font-size:8.8px;
    line-height:1.45;
    margin-top:4px;
">

                    Head Office: Jalan Dalem Kaum 76A, Regol, Kota Bandung 40251 Indonesia
                    <br>

                    Factory: Jalan Pasir Impun Mandalajati Kota Bandung 40194 Indonesia
                    <br>

                    Telp. 0877-2983-7101

                </div>

            </td>

            <!-- SLIP -->
            <td width="165" valign="top" align="right">

                <div
                    style="
                font-size:18px;
                font-weight:bold;
                margin-top:4px;
            ">
                    SLIP GAJI
                </div>

                <div style="
                font-size:11px;
                margin-top:10px;
            ">
                    {{ $payroll->period }}
                </div>

            </td>

        </tr>

    </table>

    <table width="100%" cellspacing="0" cellpadding="0" style="margin-top:1px;margin-bottom:10px;">

        <tr>
            <td style="border-bottom:2px solid #000;height:2px;"></td>
        </tr>

        <tr>
            <td style="border-bottom:1px solid #000;height:2px;"></td>
        </tr>

    </table>

    <!-- =========================
         EMPLOYEE INFO
    ========================= -->

    <table class="info">

        <tr>

            <td class="info-label">Nama</td>
            <td>: {{ $payroll->employee->name }}</td>

            <td class="info-label">Target Hari Kerja</td>
            <td>: {{ number_format($payroll->target_work_days, 0, ',', '.') }}</td>

        </tr>

        <tr>

            <td class="info-label">Jabatan</td>
            <td>: {{ $payroll->position_name ?: $payroll->employee->position?->name ?? '-' }}</td>

            <td class="info-label">Hari Kerja</td>
            <td>: {{ number_format($payroll->work_days, 0, ',', '.') }}</td>

        </tr>

        <tr>

            <td class="info-label">Departemen</td>
            <td>: {{ $payroll->department_name ?: $payroll->employee->department?->name ?? '-' }}</td>

            <td class="info-label">Jam Lembur</td>
            <td>: {{ number_format($payroll->overtime_hours, 0, ',', '.') }}</td>

        </tr>

    </table>

    <!-- DOUBLE LINE -->

    <table width="100%" cellspacing="0" cellpadding="0" style="margin-top:10px;margin-bottom:12px;">

        <tr>
            <td style="border-bottom:2px solid #000;height:2px;"></td>
        </tr>

        <tr>
            <td style="border-bottom:1px solid #000;height:2px;"></td>
        </tr>

    </table>

    <!-- =========================
         CONTENT
    ========================= -->

    <table width="100%" cellspacing="0" cellpadding="0">

        <tr>

            <!-- LEFT -->

            <td width="52%" valign="top">

                <table class="content-table" width="100%">

                    <tr>
                        <td align="center">
                            <strong>PENDAPATAN</strong>
                        </td>

                        <td width="140" align="center">
                            <strong>NOMINAL</strong>
                        </td>
                    </tr>

                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="money">Rp. {{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Tunjangan Jabatan</td>
                        <td class="money">Rp. {{ number_format($payroll->position_allowance, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Tunjangan Kehadiran</td>
                        <td class="money">Rp. {{ number_format($payroll->attendance_allowance, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Insentif Keselamatan</td>
                        <td class="money">Rp. {{ number_format($payroll->safety_incentive, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Tunjangan Risiko</td>
                        <td class="money">Rp. {{ number_format($payroll->risk_allowance, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Tunjangan Penempatan</td>
                        <td class="money">Rp. {{ number_format($payroll->placement_allowance, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Golden Shake Hand</td>
                        <td class="money">Rp. {{ number_format($payroll->golden_shake_hand, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Tunjangan PPh</td>
                        <td class="money">Rp. {{ number_format($payroll->tax_allowance, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Bonus / THR</td>
                        <td class="money">Rp. {{ number_format($payroll->irregular_income, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Uang Lembur</td>
                        <td class="money">Rp. {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td>
                    </tr>

                </table>

            </td>

            <td width="2%"></td>

            <!-- RIGHT -->

            <td width="46%" valign="top">

                <table class="content-table" width="100%">

                    <tr>
                        <td align="center">
                            <strong>POTONGAN</strong>
                        </td>

                        <td width="140" align="center">
                            <strong>NOMINAL</strong>
                        </td>
                    </tr>

                    <tr>
                        <td>BPJS Jamsostek (JHT)</td>
                        <td class="money">Rp. {{ number_format($payroll->bpjamsostek, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>BPJS Kesehatan</td>
                        <td class="money">Rp. {{ number_format($payroll->bpjs_health, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Potongan Kehadiran</td>
                        <td class="money">Rp. {{ number_format($payroll->attendance_deduction, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Denda</td>
                        <td class="money">Rp. {{ number_format($payroll->fine, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Piutang Karyawan</td>
                        <td class="money">Rp. {{ number_format($payroll->employee_receivable, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td>Objek Pajak PPh 21</td>
                        <td class="money">Rp. {{ number_format($payroll->pph21_tax_object, 0, ',', '.') }}</td>
                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <!-- =========================
         TOTAL
    ========================= -->

    <table class="total-table">

        <tr>

            <td width="35%">
                JUMLAH PENDAPATAN
            </td>

            <td class="money" width="20%">
                Rp. {{ number_format($payroll->total_income, 0, ',', '.') }}
            </td>

            <td width="25%">
                JUMLAH POTONGAN
            </td>

            <td class="money" width="20%">
                Rp. {{ number_format($payroll->total_deduction, 0, ',', '.') }}
            </td>

        </tr>

    </table>

    <!-- =========================
         TAKE HOME PAY
    ========================= -->

    <table class="net-salary">

        <tr>

            <td width="40%">
                GAJI BERSIH
            </td>

            <td class="money">
                Rp. {{ number_format($payroll->take_home_pay, 0, ',', '.') }}
            </td>

        </tr>

    </table>

    <!-- =========================
         FOOTER
    ========================= -->

    <div class="footer">
        Slip gaji ini bersifat rahasia dan hanya untuk penerima yang dituju.
    </div>

</body>

</html>
