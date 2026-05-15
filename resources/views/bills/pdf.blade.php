<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $bill->bill_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #fff;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 9pt;
            line-height: 1.3;
        }

        .invoice-box {
            width: 100%;
            margin: 0 auto;
            padding: 0;
            background: #fff;
        }

        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .company-name {
            font-size: 32px;
            font-weight: 900;
            color: #000080;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.1;
            font-family: 'Arial', sans-serif;
        }

        .company-info {
            font-size: 10pt;
            color: #000;
            margin-top: 5px;
            line-height: 1.4;
        }

        .invoice-label {
            font-size: 28px;
            font-weight: 700;
            color: #000;
            text-align: right;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 0;
            font-size: 11pt;
        }

        .meta-label {
            font-weight: 700;
            color: #000;
            text-align: right;
            padding-right: 15px;
        }

        .meta-value {
            text-align: right;
            color: #111;
        }

        .details-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .section-label {
            font-size: 9pt;
            font-weight: 800;
            color: #777;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
            margin-bottom: 8px;
            display: block;
        }

        .customer-name {
            font-size: 14pt;
            font-weight: 700;
            color: #000080;
            margin-bottom: 3px;
        }

        .customer-address {
            font-size: 10pt;
            color: #333;
            line-height: 1.5;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 0.5pt solid #000;
        }

        .items-table th {
            background-color: #f8f9fa;
            color: #000;
            font-size: 9pt;
            font-weight: 800;
            text-transform: uppercase;
            padding: 8px 6px;
            border-bottom: 1.5pt solid #000;
            border-right: 0.5pt solid #000;
            text-align: center;
        }

        .items-table td {
            padding: 6px 8px;
            border-bottom: 0.5pt solid #000;
            border-right: 0.5pt solid #000;
            font-size: 10pt;
            vertical-align: middle;
            color: #000;
        }

        .items-table th:last-child, .items-table td:last-child {
            border-right: none;
        }

        .item-desc {
            font-weight: 700;
            color: #000;
            font-size: 8.5pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            width: 100%;
        }

        .item-remarks {
            font-size: 8pt;
            color: #555;
            margin-top: 1px;
            display: block;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 4px 0;
            font-size: 10pt;
            color: #000;
        }

        .summary-label {
            color: #000;
            text-align: right;
            padding-right: 15px;
        }

        .summary-value {
            font-weight: 700;
            color: #000;
            text-align: right;
        }

        .total-row td {
            border-top: 1.5pt solid #696cff;
            padding-top: 10px;
        }

        .total-label {
            font-size: 16pt;
            color: #000;
            font-weight: 800;
        }

        .total-value {
            font-size: 22pt;
            color: #696cff;
            font-weight: 900;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #444;
            border-top: 0.5pt solid #000;
            padding-top: 3mm;
            background: #fff;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    @php
        // Try to use direct path for PDF to keep size small, fallback to base64 if needed
        $logoPath = '';
        if ($companySetting && $companySetting->logo_path) {
            $fullPath = public_path('storage/' . $companySetting->logo_path);
            if (file_exists($fullPath)) {
                $logoPath = $fullPath;
            }
        }
    @endphp

    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 65%; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            @if($logoPath)
                                <td style="width: 80px; vertical-align: top; padding-right: 15px;">
                                    <img src="{{ $logoPath }}" alt="Logo" style="max-height: 80px; width: auto;">
                                </td>
                            @endif
                            <td style="vertical-align: top;">
                                <div class="company-name">{{ $companySetting->name }}</div>
                                <div class="company-info">
                                    {!! nl2br(e($companySetting->address)) !!}<br>
                                    @if($companySetting->phone) Ph: {{ $companySetting->phone }} @endif
                                    @if($companySetting->email) | Email: {{ $companySetting->email }} @endif
                                    @if($companySetting->tax_number) <br><strong>STR / NTN: {{ $companySetting->tax_number }}</strong> @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%; vertical-align: top;">
                    <div class="invoice-label">INVOICE</div>
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">No:</td>
                            <td class="meta-value"><strong>{{ $bill->bill_number }}</strong></td>
                        </tr>
                        <tr>
                            <td class="meta-label">Date:</td>
                            <td class="meta-value">{{ $bill->bill_date->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Details -->
        <table class="details-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <span class="section-label">BILL TO:</span>
                    <div class="customer-name">{{ optional($bill->customer)->name ?? 'Walk-in Customer' }}</div>
                    <div class="customer-address">
                        {!! nl2br(e(optional($bill->customer)->address)) !!}
                        @if(optional($bill->customer)->phone) <br>Ph: {{ $bill->customer->phone }} @endif
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <span class="section-label">ACCOUNT SUMMARY:</span>
                    @php
                        $totalPaid = $bill->payments->sum('amount');
                        if ($totalPaid >= $bill->total) {
                            $statusText = 'FULLY PAID';
                            $statusColor = '#28a745';
                        } elseif ($totalPaid > 0) {
                            $statusText = 'PARTIALLY PAID';
                            $statusColor = '#ffab00';
                        } else {
                            $statusText = 'UNPAID / ON ACCOUNT';
                            $statusColor = '#ff3e1d';
                        }
                    @endphp
                    <div style="font-size: 11pt; color: #444;">Status: <strong style="color: {{ $statusColor }};">{{ $statusText }}</strong></div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">S#</th>
                    <th style="text-align: center;">DESCRIPTION</th>
                    <th style="width: 80px; text-align: center;">DELIVERY</th>
                    <th style="width: 70px; text-align: center;">QTY</th>
                    <th style="width: 70px; text-align: center;">RATE</th>
                    <th style="width: 90px; text-align: center;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php $lineNo = 1; @endphp
                @foreach($bill->billItems as $item)
                <tr>
                    <td class="text-center">{{ $lineNo++ }}</td>
                    <td>
                        <span class="item-desc">{{ optional($item->item)->name ?? 'Product Deleted' }}</span>
                        @if($item->remarks)
                            <span class="item-remarks">{{ $item->remarks }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $item->delivery_date ? \Carbon\Carbon::parse($item->delivery_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="text-right">{{ number_format($item->quantity) }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right" style="font-weight: 700;">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach

                @if($bill->billExpenses->count() > 0)
                <tr style="background: #fafafa;">
                    <td colspan="3" style="text-align: right; font-weight: 700; font-size: 8pt;">ITEM SUBTOTAL</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($bill->billItems->sum('quantity')) }}</td>
                    <td></td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($bill->billItems->sum('total'), 2) }}</td>
                </tr>

                @foreach($bill->billExpenses as $expense)
                <tr>
                    <td class="text-center" style="color: #696cff;">+</td>
                    <td colspan="4">
                        <span class="item-desc" style="color: #696cff;">{{ $expense->description }}</span>
                    </td>
                    <td class="text-right" style="font-weight: 700; color: #696cff;">{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

        <!-- Summary Section -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td style="width: 55%; vertical-align: top; padding-right: 20px;">
                    <div style="font-style: italic; font-size: 10pt; color: #555; border-top: 0.5pt solid #eee; padding-top: 5px;">
                        <strong>Amount in Words:</strong><br>
                        {{ $bill->amount_in_words() }}
                    </div>
                </td>
                <td style="width: 45%; vertical-align: top;">
                    <table class="summary-table">
                        @if($bill->billExpenses->count() === 0)
                        <tr>
                            <td class="summary-label">Subtotal:</td>
                            <td class="summary-value">{{ number_format($bill->billItems->sum('total'), 2) }}</td>
                        </tr>
                        @endif

                        @if($bill->discount > 0)
                        <tr>
                            <td class="summary-label">Discount:</td>
                            <td class="summary-value" style="color: #d9534f;">- {{ number_format($bill->discount, 2) }}</td>
                        </tr>
                        @endif

                        @if($bill->tax > 0)
                        <tr>
                            <td class="summary-label">Tax:</td>
                            <td class="summary-value">+ {{ number_format($bill->tax, 2) }}</td>
                        </tr>
                        @endif

                        <tr class="total-row">
                            <td class="summary-label total-label">Grand Total:</td>
                            <td class="summary-value total-value">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bill->total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for choosing <strong>{{ $companySetting->name }}</strong>! This is a system-generated document and does not require a signature.</p>
        </div>
    </div>

</body>
</html>
