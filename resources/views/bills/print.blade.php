<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill - {{ $bill->bill_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .invoice-box {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .footer {
                position: fixed;
                bottom: 0mm;
                left: 0;
                right: 0;
                text-align: center;
                background: #fff;
                border-top: 1.5pt solid #000;
                padding-top: 15px;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f6;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .no-print {
            max-width: 900px;
            margin: 0 auto 20px auto;
            text-align: center;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 14px;
            margin: 0 5px;
        }

        .btn-print {
            background: #696cff;
            color: #fff;
        }

        .btn-back {
            background: #8592a3;
            color: #fff;
        }

        .invoice-box {
            max-width: 900px;
            margin: 0 auto;
            padding: 50px;
            background: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 40px;
            border-collapse: collapse;
        }

        .company-name {
            font-size: 42px;
            font-weight: 900;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1;
        }

        .company-info {
            font-size: 14px;
            color: #000;
            margin-top: 5px;
            line-height: 1.4;
        }

        .invoice-label {
            font-size: 34px;
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
            padding: 4px 0;
            font-size: 15px;
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
            margin-bottom: 40px;
            border-collapse: collapse;
        }

        .section-label {
            font-size: 11px;
            font-weight: 800;
            color: #999;
            text-transform: uppercase;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
            display: block;
        }

        .customer-name {
            font-size: 18px;
            font-weight: 700;
            color: #000;
            margin-bottom: 5px;
        }

        .customer-address {
            font-size: 14px;
            color: #444;
            line-height: 1.6;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1pt solid #000;
        }

        .items-table th {
            background-color: #f8f9fa;
            color: #000;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 10px 8px;
            border-bottom: 2pt solid #000;
            border-right: 1pt solid #000;
            text-align: left;
        }

        .items-table td {
            padding: 8px 10px;
            border-bottom: 1pt solid #000;
            border-right: 1pt solid #000;
            font-size: 12px;
            vertical-align: middle;
            color: #000;
        }

        .items-table th:last-child, .items-table td:last-child {
            border-right: none;
        }

        .item-desc {
            font-weight: 700;
            color: #000;
            white-space: nowrap;
        }

        .item-remarks {
            font-size: 11px;
            color: #000;
            margin-top: 2px;
            display: block;
        }

        .summary-container {
            width: 100%;
            display: table;
            margin-top: 20px;
        }

        .summary-wrapper {
            float: right;
            width: 380px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 0;
            font-size: 13px;
            color: #000;
            white-space: nowrap;
        }

        .summary-label {
            color: #000;
            text-align: right;
            padding-right: 20px;
            width: auto;
        }

        .summary-value {
            font-weight: 700;
            color: #000;
            text-align: right;
            width: auto;
        }

        .total-row td {
            border-top: 2px solid #696cff;
            padding-top: 15px;
        }

        .total-label {
            font-size: 18px;
            color: #566a7f;
        }

        .total-value {
            font-size: 24px;
            color: #696cff;
            font-weight: 800;
        }

        .footer {
            margin-top: 80px;
            text-align: center;
            font-size: 12px;
            color: #000;
            border-top: 1px solid #000;
            padding-top: 20px;
        }
        
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">Print Invoice</button>
        <a href="{{ route('bills.show', $bill) }}" class="btn btn-back">Back to App</a>
    </div>

    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 70%; vertical-align: top;">
                    <div style="display: flex; align-items: center;">
                        @if(optional($companySetting)->logo_path)
                            <div style="margin-right: 25px;">
                                <img src="{{ asset('storage/' . $companySetting->logo_path) }}" alt="Logo" style="max-height: 90px; max-width: 90px; object-fit: contain;">
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <div class="company-name" style="margin-bottom: 2px;">{{ $companySetting->name }}</div>
                            <div class="company-info" style="font-size: 14px; line-height: 1.4;">
                                {!! nl2br(e($companySetting->address)) !!}<br>
                                @if($companySetting->phone) Ph: {{ $companySetting->phone }} @endif
                                @if($companySetting->email) | Email: {{ $companySetting->email }} @endif
                                @if($companySetting->website) | Web: {{ $companySetting->website }} @endif
                                @if($companySetting->tax_number) <br><strong>STR / NTN: {{ $companySetting->tax_number }}</strong> @endif
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top;">
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
                    <span class="section-label" style="text-align: right;">ACCOUNT SUMMARY:</span>
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
                    <div style="font-size: 14px; color: #444;">Status: <strong style="color: {{ $statusColor }};">{{ $statusText }}</strong></div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">S. NO.</th>
                    <th>DESCRIPTION</th>
                    <th style="width: 110px; text-align: center;">DELIVERY</th>
                    <th style="width: 90px; text-align: right;">QTY</th>
                    <th style="width: 90px; text-align: right;">RATE</th>
                    <th style="width: 120px; text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php $lineNo = 1; @endphp
                @foreach($bill->billItems as $item)
                <tr>
                    <td style="text-align: center; color: #000; font-weight: normal;">{{ $lineNo++ }}</td>
                    <td>
                        <span class="item-desc">{{ optional($item->item)->name ?? 'Product Deleted' }}</span>
                        @if($item->remarks)
                            <span class="item-remarks">{{ $item->remarks }}</span>
                        @endif
                    </td>
                    <td style="text-align: center; color: #000;">
                        {{ $item->delivery_date ? \Carbon\Carbon::parse($item->delivery_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td style="text-align: right;">{{ number_format($item->quantity) }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach

                <!-- Subtotal / Expense Divider -->
                @if($bill->billExpenses->count() > 0)
                <tr style="background: #fafafa;">
                    <td colspan="3" style="text-align: right; font-weight: 700; color: #000; font-size: 11px;">ITEM SUBTOTAL</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($bill->billItems->sum('quantity')) }}</td>
                    <td></td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($bill->billItems->sum('total'), 2) }}</td>
                </tr>

                @foreach($bill->billExpenses as $expense)
                <tr>
                    <td style="text-align: center; color: #696cff;">+</td>
                    <td colspan="4">
                        <span class="item-desc" style="color: #696cff;">{{ $expense->description }}</span>
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #696cff;">{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

        <!-- Summary Section -->
        <div style="width: 100%; margin-top: 30px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 55%; vertical-align: bottom; text-align: left; padding-bottom: 5px;">
                        <div style="font-style: italic; font-size: 15px; color: #566a7f; border-top: 1px solid #f0f0f0; padding-top: 10px;">
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
        </div>

        <div class="footer">
            <p>Thank you for choosing <strong>{{ $companySetting->name }}</strong>! This is a system-generated document and does not require a signature.</p>
            @if($companySetting->other_details)
                <p style="font-size: 11px; margin-top: 5px; color: #566a7f;">{{ $companySetting->other_details }}</p>
            @endif
        </div>
    </div>

    <script>
        // Auto print on load but only in the popup window if appropriate
        // (Removing auto-print to allow manual control since it's a dedicated page now)
        // window.print(); 
    </script>
</body>
</html>
