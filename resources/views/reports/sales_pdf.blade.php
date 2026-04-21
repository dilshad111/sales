<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
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
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-details {
            font-size: 8.5pt;
            color: #555;
            margin: 2px 0;
        }
        .report-title-container {
            text-align: center;
            margin: 15px 0;
        }
        .report-title {
            display: inline-block;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
            background-color: #2c3e50;
            padding: 5px 25px;
            border-radius: 4px;
        }
        .metrics-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .metric-box {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: center;
            background-color: #fcfcfc;
        }
        .metric-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            color: #7f8c8d;
            margin-bottom: 4px;
            font-weight: bold;
        }
        .metric-value {
            font-size: 13pt;
            font-weight: bold;
        }
        .val-sales { color: #27ae60; }
        .val-payments { color: #2980b9; }
        .val-outstanding { color: #d35400; }

        h2 {
            font-size: 11pt;
            border-left: 5px solid #2c3e50;
            padding-left: 10px;
            margin: 20px 0 10px 0;
            color: #2c3e50;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        th {
            background-color: #f1f1f1;
            color: #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 8px;
            border: 1px solid #ccc;
            text-align: center;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #eee;
            word-wrap: break-word;
            vertical-align: middle;
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
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 7pt;
            color: #95a5a6;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .total-row {
            background-color: #f1f1f1 !important;
            font-weight: bold;
            color: #000;
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
                <div class="company-details">STRN/NTN: {{ $companySetting->tax_number }}</div>
            @endif
        </div>

        <div class="report-title-container">
            <div class="report-title">Sales Report Overview</div>
        </div>

        <table class="metrics-table" style="border: none;">
            <tr>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Total Sales</div>
                    <div class="metric-value val-sales">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalSales, 2) }}</div>
                </td>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Payments Received</div>
                    <div class="metric-value val-payments">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalPayments, 2) }}</div>
                </td>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Total Outstanding</div>
                    <div class="metric-value val-outstanding">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($totalOutstanding, 2) }}</div>
                </td>
            </tr>
        </table>

        <h2>Item-wise Sales</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 60%;">Item Name</th>
                    <th style="width: 15%;">Quantity</th>
                    <th style="width: 25%;">Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemSales as $itemData)
                <tr>
                    <td>{{ $itemData['item']->name }}</td>
                    <td class="text-center">{{ number_format($itemData['quantity'], 0) }}</td>
                    <td class="text-right">{{ number_format($itemData['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="text-center">{{ number_format(array_sum(array_column($itemSales, 'quantity')), 0) }}</td>
                    <td class="text-right">{{ number_format(array_sum(array_column($itemSales, 'total')), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 10px;"></div>

        <h2>Bill Details</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Bill #</th>
                    <th style="width: 28%;">Customer</th>
                    <th style="width: 14%;">Date</th>
                    <th style="width: 16%;">Total Sales</th>
                    <th style="width: 16%;">Total Paid</th>
                    <th style="width: 16%;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                <tr>
                    <td class="text-center">{{ $bill->bill_number }}</td>
                    <td>{{ $bill->customer->name }}</td>
                    <td class="text-center">{{ $bill->bill_date->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($bill->total, 2) }}</td>
                    <td class="text-right">{{ number_format($bill->payments->sum('amount'), 2) }}</td>
                    <td class="text-right">{{ number_format($bill->total - $bill->payments->sum('amount'), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($bills->sum('total'), 2) }}</td>
                    <td class="text-right">{{ number_format($bills->sum(fn($b) => $b->payments->sum('amount')), 2) }}</td>
                    <td class="text-right">{{ number_format($bills->sum(fn($b) => $b->total - $b->payments->sum('amount')), 2) }}</td>
                </tr>
            </tfoot>
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
