<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Statement Report</title>
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
            line-height: 1.4;
        }
        .container {
            padding: 10px 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 15px;
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
            margin: 10px 0;
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
        .summary-row {
            margin-bottom: 20px;
        }
        .metric-card {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: center;
            background-color: #fcfcfc;
            width: 48%;
            float: left;
        }
        .metric-card.right { float: right; }
        .metric-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            color: #7f8c8d;
            margin-bottom: 4px;
            font-weight: bold;
        }
        .metric-value {
            font-size: 14pt;
            font-weight: bold;
        }
        .val-received { color: #27ae60; }
        .val-receivable { color: #c0392b; }

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
    <div class="container">
        <div class="header">
            @if(extension_loaded('gd') && $companySetting->logo_path)
                <img src="{{ storage_path('app/public/' . $companySetting->logo_path) }}" class="logo">
            @endif
            <div class="company-name">{{ $companySetting->name }}</div>
            <div class="company-details">{{ $companySetting->address }}</div>
            <div class="company-details">Phone: {{ $companySetting->phone }} | Email: {{ $companySetting->email }}</div>
            <div class="company-details">Period: {{ $startDate ?: 'All' }} to {{ $endDate ?: 'Present' }}</div>
        </div>

        <div class="report-title-container">
            <div class="report-title">Cash Statement Report</div>
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
