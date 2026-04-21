<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $bill->bill_number }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
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
        .invoice-banner {
            text-align: center;
            margin: 15px 0;
        }
        .invoice-title {
            display: inline-block;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
            background-color: #2c3e50;
            padding: 5px 30px;
            border-radius: 4px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .section-title {
            font-size: 8pt;
            font-weight: bold;
            color: #7f8c8d;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            margin-bottom: 5px;
            padding-bottom: 2px;
        }
        .info-content {
            font-size: 10pt;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f1f1f1;
            color: #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 10px 8px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .items-table td {
            padding: 8px;
            border: 1px solid #eee;
            word-wrap: break-word;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-wrapper {
            float: right;
            width: 40%;
        }
        .summary-table td {
            border: none;
            padding: 4px 0;
        }
        .grand-total {
            font-size: 14pt;
            font-weight: bold;
            color: #2c3e50;
            border-top: 2px solid #2c3e50 !important;
            padding-top: 10px !important;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 7.5pt;
            color: #95a5a6;
            border-top: 1px solid #eee;
            padding-top: 10px;
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
            @if($companySetting->tax_number)
                <div class="company-details">STRN/NTN: {{ $companySetting->tax_number }}</div>
            @endif
        </div>

        <div class="invoice-banner">
            <div class="invoice-title">Sales Invoice</div>
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <div class="section-title">Bill To</div>
                    <div class="info-content">
                        <strong>{{ optional($bill->customer)->name ?? 'Walk-in Customer' }}</strong><br>
                        {{ optional($bill->customer)->address }}<br>
                        Ph: {{ optional($bill->customer)->phone }}
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="section-title" style="text-align: right;">Invoice Details</div>
                    <div class="info-content">
                        <strong>Invoice #: {{ $bill->bill_number }}</strong><br>
                        Date: {{ $bill->bill_date->format('d/m/Y') }}<br>
                        @php $totalPaid = $bill->payments->sum('amount'); @endphp
                        Status: <span style="color: {{ $totalPaid >= $bill->total ? '#27ae60' : ($totalPaid > 0 ? '#f39c12' : '#c0392b') }}">
                            {{ $totalPaid >= $bill->total ? 'FULLY PAID' : ($totalPaid > 0 ? 'PARTIALLY PAID' : 'UNPAID') }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">S#</th>
                    <th style="width: 45%;">Description</th>
                    <th style="width: 15%;">Quantity</th>
                    <th style="width: 15%;">Rate</th>
                    <th style="width: 20%;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->billItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ optional($item->item)->name }}</strong>
                        @if($item->remarks) <br><small style="color: #666;">{{ $item->remarks }}</small> @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity) }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right fw-bold">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
                @foreach($bill->billExpenses as $expense)
                <tr>
                    <td class="text-center text-muted">+</td>
                    <td colspan="3">{{ $expense->description }} (Additional Expense)</td>
                    <td class="text-right fw-bold">{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

            <div style="float: left; width: 55%;">
                <div class="section-title" style="margin-bottom: 8px;">Amount in Words (PKR)</div>
                <div style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; border-left: 3px solid #7f8c8d; font-style: italic; font-weight: 500; font-size: 9.5pt; color: #333;">
                    {{ $bill->amount_in_words() }}
                </div>
            </div>
            <div class="summary-wrapper">
                <table class="summary-table" style="width: 100%;">
                    <tr>
                        <td class="text-right">Subtotal:</td>
                        <td class="text-right" style="width: 40%;">{{ number_format($bill->billItems->sum('total') + $bill->billExpenses->sum('amount'), 2) }}</td>
                    </tr>
                    @if($bill->discount > 0)
                    <tr>
                        <td class="text-right">Discount:</td>
                        <td class="text-right" style="color: #c0392b;">-{{ number_format($bill->discount, 2) }}</td>
                    </tr>
                    @endif
                    @if($bill->tax > 0)
                    <tr>
                        <td class="text-right">Tax {{ $bill->tax_percent > 0 ? '(' . number_format($bill->tax_percent, 2) . '%)' : '' }}:</td>
                        <td class="text-right">+{{ number_format($bill->tax, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-right grand-total">Grand Total:</td>
                        <td class="text-right grand-total">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bill->total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            Thank you for your business! This is a system-generated document.
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
