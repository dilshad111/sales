<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Statement</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
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
        .customer-info-box {
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-left: 4px solid #2c3e50;
        }
        .customer-info-box p {
            margin: 2px 0;
        }
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
        .opening-row {
            background-color: #e3f2fd !important;
            font-weight: bold;
        }
        .payment-row {
            background-color: #e8f5e9 !important;
        }
        .text-right {
            text-align: right;
            white-space: nowrap;
        }
        .text-center {
            text-align: center;
            white-space: nowrap;
        }
        .text-success { color: #2e7d32; }
        .text-danger { color: #c62828; }
        
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
        </div>

        <div class="report-title-container">
            <div class="report-title">Customer Statement</div>
        </div>

        @if($customer)
        <div class="customer-info-box">
            <p><strong>Customer:</strong> {{ $customer->name }}</p>
            <p><strong>Phone:</strong> {{ $customer->phone }} | <strong>Email:</strong> {{ $customer->email }}</p>
            @if($startDate || $endDate)
                <p><strong>Period:</strong> 
                    @if($startDate && $endDate) {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    @elseif($startDate) From {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                    @elseif($endDate) Up to {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    @endif
                </p>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 10%;">Bill #</th>
                    <th style="width: 28%;">Description</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 14%;">Sales</th>
                    <th style="width: 12%;">Paid</th>
                    <th style="width: 14%;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr class="{{ $tx['type'] == 'opening_balance' ? 'opening-row' : ($tx['type'] == 'payment' ? 'payment-row' : '') }}">
                    <td class="text-center">{{ \Carbon\Carbon::parse($tx['date'])->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $tx['bill_no'] }}</td>
                    <td>{{ $tx['description'] }}</td>
                    <td class="text-center">{{ $tx['quantity'] !== '-' ? number_format($tx['quantity'], 0) : '-' }}</td>
                    <td class="text-right">{{ $tx['sales_amount'] > 0 ? number_format($tx['sales_amount'], 2) : '-' }}</td>
                    <td class="text-right text-success">{{ $tx['payment_received'] > 0 ? number_format($tx['payment_received'], 2) : '-' }}</td>
                    <td class="text-right font-weight-bold {{ $tx['balance'] > 0 ? 'text-danger' : ($tx['balance'] < 0 ? 'text-success' : '') }}">
                        {{ number_format($tx['balance'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($transactions->where('type', '!=', 'opening_balance')->sum('sales_amount'), 2) }}</td>
                    <td class="text-right text-success">{{ number_format($transactions->where('type', '!=', 'opening_balance')->sum('payment_received'), 2) }}</td>
                    <td class="text-right {{ $transactions->last()['balance'] > 0 ? 'text-danger' : ($transactions->last()['balance'] < 0 ? 'text-success' : '') }}">
                        {{ number_format($transactions->last()['balance'], 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
        @endif

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
