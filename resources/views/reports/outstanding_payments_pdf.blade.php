<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Outstanding Payments Report</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5pt;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .container {
            padding: 10px 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            max-height: 70px;
            margin-bottom: 5px;
        }
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-details {
            font-size: 8pt;
            color: #555;
            margin: 2px 0;
        }
        .report-title-container {
            text-align: center;
            margin: 15px 0;
        }
        .report-title {
            display: inline-block;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
            background-color: #2c3e50;
            padding: 4px 20px;
            border-radius: 4px;
        }
        .metrics-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .metric-box {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
            background-color: #fcfcfc;
        }
        .metric-label {
            font-size: 7pt;
            text-transform: uppercase;
            color: #7f8c8d;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .metric-value {
            font-size: 12pt;
            font-weight: bold;
        }
        .val-billed { color: #4e73df; }
        .val-paid { color: #1cc88a; }
        .val-outstanding { color: #f6c23e; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        th {
            background-color: #f1f1f1;
            color: #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5pt;
            padding: 7px;
            border: 1px solid #ccc;
            text-align: center;
        }
        td {
            padding: 5px;
            border: 1px solid #eee;
            word-wrap: break-word;
            vertical-align: middle;
            font-size: 8pt;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
            white-space: nowrap;
        }
        .text-center {
            text-align: center;
            white-space: nowrap;
        }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .bg-success { background-color: #d4edda; color: #155724; }
        .bg-warning { background-color: #fff3cd; color: #856404; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 6.5pt;
            color: #95a5a6;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        .total-row {
            background-color: #f1f1f1 !important;
            font-weight: bold;
            color: #000;
        }
        .aging-table {
            width: 70%;
            margin: 20px auto;
        }
        .section-header {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #2c3e50;
            margin-bottom: 8px;
            border-bottom: 1px solid #2c3e50;
            padding-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($companySetting->logo_path)
                <img src="{{ public_path('storage/' . $companySetting->logo_path) }}" class="logo">
            @endif
            <div class="company-name" style="font-size: 28pt; letter-spacing: 2px;">{{ $companySetting->name }}</div>
            <div class="company-details" style="font-size: 11pt; font-weight: bold;">{{ $companySetting->address }}</div>
            <div class="company-details">Phone: {{ $companySetting->phone }} | Email: {{ $companySetting->email }}</div>
            @if($companySetting->tax_number)
                <div class="company-details">Tax Reg: {{ $companySetting->tax_number }}</div>
            @endif
        </div>

        <div class="report-title-container">
            <div class="report-title">Outstanding Payments</div>
        </div>

        <table class="metrics-table" style="border: none;">
            <tr>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Total Billed Amt</div>
                    <div class="metric-value val-billed">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($summary['total_billed'], 2) }}</div>
                </td>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Total Collected</div>
                    <div class="metric-value val-paid">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($summary['total_paid'], 2) }}</div>
                </td>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Total Outstanding</div>
                    <div class="metric-value val-outstanding">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($summary['total_outstanding'], 2) }}</div>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Bill #</th>
                    <th style="width: 29%;">Customer</th>
                    <th style="width: 11%;">Date</th>
                    <th style="width: 15%;">Total</th>
                    <th style="width: 15%;">Paid</th>
                    <th style="width: 15%;">Outstanding</th>
                    <th style="width: 5%;">St</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $data)
                <tr>
                    <td class="text-center">{{ $data['bill']->bill_number }}</td>
                    <td>{{ $data['bill']->customer->name }}</td>
                    <td class="text-center">{{ $data['bill']->bill_date->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($data['bill']->total, 2) }}</td>
                    <td class="text-right">{{ number_format($data['paid'], 2) }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($data['outstanding'], 2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $data['status'] == 'paid' ? 'bg-success' : ($data['status'] == 'partially_paid' ? 'bg-warning' : 'bg-danger') }}">
                            {{ $data['status'] == 'paid' ? 'P' : ($data['status'] == 'partially_paid' ? 'PP' : 'O') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($bills->sum(fn($b) => $b['bill']->total), 2) }}</td>
                    <td class="text-right">{{ number_format($bills->sum('paid'), 2) }}</td>
                    <td class="text-right">{{ number_format($bills->sum('outstanding'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        @php
            $agingSummary = [
                '1-30' => $bills->where('aging_bucket', '1-30')->sum('outstanding'),
                '31-60' => $bills->where('aging_bucket', '31-60')->sum('outstanding'),
                '61-90' => $bills->where('aging_bucket', '61-90')->sum('outstanding'),
                '91+' => $bills->where('aging_bucket', '91+')->sum('outstanding'),
            ];
        @endphp

        <div class="section-header">Aging Summary</div>
        <table class="aging-table">
            <thead>
                <tr>
                    <th>1-30 Days</th>
                    <th>31-60 Days</th>
                    <th>61-90 Days</th>
                    <th>91+ Days</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-right">{{ number_format($agingSummary['1-30'], 2) }}</td>
                    <td class="text-right">{{ number_format($agingSummary['31-60'], 2) }}</td>
                    <td class="text-right">{{ number_format($agingSummary['61-90'], 2) }}</td>
                    <td class="text-right" style="color: #c0392b; font-weight: bold;">{{ number_format($agingSummary['91+'], 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            Generated on {{ now()->format('d/m/Y h:i A') }}
        </div>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $x = 520;
            $y = 820;
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 8;
            $color = array(0.3, 0.3, 0.3);
            $word_space = 0.0;
            $char_space = 0.0;
            $angle = 0.0;
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>
</html>
