<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Statement Report</title>
    <style>
        @page {
            margin: 0.8cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .container {
            padding: 0;
        }
        .header {
            border-bottom: 3px solid #1a2a6c;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            padding: 0;
            vertical-align: top;
            background: transparent;
        }
        .logo {
            max-height: 80px;
        }
        .company-info {
            padding-left: 20px;
        }
        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #1a2a6c;
            margin: 0;
            text-transform: uppercase;
            line-height: 1;
        }
        .company-details {
            font-size: 9pt;
            color: #555;
            margin: 4px 0 0 0;
            line-height: 1.3;
        }
        .report-info {
            text-align: right;
        }
        .report-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1a2a6c;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .period-box {
            margin-top: 10px;
            font-size: 8.5pt;
            color: #444;
            background: #f8f9fa;
            padding: 5px 10px;
            display: inline-block;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        .summary-row {
            margin-bottom: 25px;
        }
        .metric-card {
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: center;
            background-color: #ffffff;
            width: 48%;
            float: left;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .metric-card.right { float: right; }
        .metric-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 6px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .metric-value {
            font-size: 16pt;
            font-weight: bold;
        }
        .val-received { color: #2e7d32; }
        .val-receivable { color: #d32f2f; }

        .customer-section {
            margin-bottom: 25px;
            border: 1px solid #eee;
            border-radius: 5px;
            overflow: hidden;
            background-color: #fff;
        }
        .customer-title {
            background-color: #2c3e50;
            color: white;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 9.5pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer-total {
            background-color: #f8f9fa !important;
            font-weight: bold;
        }
        .party-summary-title {
            font-size: 10pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #2c3e50;
            clear: both;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 6.5pt;
            color: #95a5a6;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    @php
        // Logo Base64 for PDF
        $logoBase64 = '';
        if ($companySetting->logo_path) {
            $logoPath = public_path('storage/' . $companySetting->logo_path);
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
    @endphp
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td style="width: 15%;">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" class="logo">
                        @endif
                    </td>
                    <td class="company-info">
                        <div class="company-name">{{ $companySetting->name }}</div>
                        <div class="company-details">{{ $companySetting->address }}</div>
                        <div class="company-details">Phone: {{ $companySetting->phone }} | Email: {{ $companySetting->email }}</div>
                    </td>
                    <td class="report-info">
                        <div class="report-title">Cash Statement</div>
                        <div class="period-box">
                            Period: <strong>{{ $startDate ?: 'All' }}</strong> to <strong>{{ $endDate ?: 'Present' }}</strong>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="summary-row clearfix">
            <div class="metric-card">
                <div class="metric-label">Total Amount Received</div>
                <div class="metric-value val-received">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalReceived, 2) }}</div>
            </div>
            <div class="metric-card right">
                <div class="metric-label">Total Amount Receivable</div>
                <div class="metric-value val-receivable">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalReceivable, 2) }}</div>
            </div>
        </div>

        @foreach($reports as $report)
        <div class="customer-section">
            <div class="customer-title">
                <table style="width: 100%; border: none; margin: 0;">
                    <tr style="border: none;">
                        <td style="border: none; background: transparent; color: white;">Customer: {{ $report['customer']->name }}</td>
                        <td style="border: none; background: transparent; color: white; text-align: right;">Contact: {{ $report['customer']->phone }}</td>
                    </tr>
                </table>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">S#</th>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 15%;">Mode</th>
                        <th style="width: 25%;">Remarks</th>
                        <th style="width: 25%;">Payment Party</th>
                        <th style="width: 15%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['payments'] as $idx => $payment)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ ucfirst($payment->mode) }}</td>
                        <td>{{ $payment->remarks ?: '-' }}</td>
                        <td class="text-center">{{ $payment->paymentParty->name ?? '-' }}</td>
                        <td class="text-right fw-bold">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-2">No payments in this period.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="footer-total">
                        <td colspan="5" class="text-right">SUB TOTAL:</td>
                        <td class="text-right">{{ number_format($report['subtotal'], 2) }}</td>
                    </tr>
                    <tr class="footer-total" style="color: #c0392b;">
                        <td colspan="5" class="text-right font-weight-bold">OUTSTANDING:</td>
                        <td class="text-right">{{ number_format($report['outstanding'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endforeach

        <div class="party-summary-title">Party-wise Collection Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th style="text-align: left;">Payment Party</th>
                    <th style="width: 30%;">Total Collected</th>
                </tr>
            </thead>
            <tbody>
                @foreach($partySummary as $pSummary)
                <tr>
                    <td>{{ $pSummary['name'] }}</td>
                    <td class="text-right fw-bold">{{ $pSummary['amount'] > 0 ? number_format($pSummary['amount'], 2) : '-' }}</td>
                </tr>
                @endforeach
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
