<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - #{{ $payment->id }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 5mm; /* Centering the 200mm width on A4 (210mm) */
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
            .receipt-box {
                box-shadow: none !important;
                border: 0.5pt solid #eee !important;
                margin: 0 auto !important;
                padding: 4mm 6mm !important;
                width: 200mm !important;
                height: 125mm !important;
                box-sizing: border-box;
            }
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f6;
            color: #2b3e50;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
            font-size: 13px;
        }

        .no-print {
            max-width: 600px;
            margin: 0 auto 15px auto;
            text-align: center;
            background: #fff;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .btn {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 13px;
            margin: 0 4px;
            transition: all 0.2s;
        }

        .btn-print {
            background: #696cff;
            color: #fff;
        }

        .btn-back {
            background: #8592a3;
            color: #fff;
        }

        .receipt-box {
            width: 200mm;
            height: 125mm;
            margin: 0 auto;
            padding: 6mm;
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            border-radius: 2px;
            position: relative;
            border: 1px solid #e1e4e8;
            box-sizing: border-box;
        }

        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2pt solid #696cff;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .voucher-title {
            color: #696cff;
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-main {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 10px;
        }

        .company-info {
            margin-bottom: 0;
            width: 50%;
        }

        .company-name {
            font-size: 15px;
            font-weight: 800;
            color: #1c1c1c;
            line-height: 1.1;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .company-details {
            font-size: 10px;
            color: #8592a3;
            max-width: 250px;
        }

        .meta-info {
            background: #fdfdfd;
            border: 1px solid #f0f2f5;
            padding: 6px;
            border-radius: 6px;
            min-width: 120px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-label {
            font-size: 8px;
            color: #a1acb8;
            text-transform: uppercase;
            font-weight: 700;
        }

        .meta-value {
            font-size: 11px;
            color: #566a7f;
            font-weight: 700;
            padding-bottom: 4px;
        }

        .section-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #a1acb8;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .customer-info {
            margin-bottom: 5px;
            width: 50%;
            text-align: right;
        }

        .customer-name {
            font-size: 13px;
            font-weight: 800;
            color: #435971;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .details-table th {
            background: #f8f9fa;
            padding: 4px 8px;
            text-align: left;
            font-size: 9px;
            color: #566a7f;
            text-transform: uppercase;
            border-bottom: 1.5pt solid #ebedef;
        }

        .details-table td {
            padding: 4px 8px;
            border-bottom: 1pt solid #f0f2f5;
            font-size: 11px;
        }

        .amount-container {
            background: #f4f6ff;
            border-radius: 6px;
            border-left: 4px solid #696cff;
            padding: 6px 10px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .amount-words {
            font-size: 11px;
            color: #566a7f;
            font-style: italic;
            width: 65%;
        }

        .amount-block {
            text-align: right;
            width: 30%;
        }

        .amount-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #8592a3;
        }

        .amount-value {
            font-size: 16px;
            font-weight: 800;
            color: #696cff;
        }

        .remark-box {
            font-size: 10px;
            color: #566a7f;
            border-bottom: 1px dotted #dcdfe3;
            padding-bottom: 2px;
            margin-bottom: 10px;
        }

        .signature-grid {
            position: absolute;
            bottom: 8mm;
            left: 10mm;
            right: 10mm;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print();" class="btn btn-print">
            <i class="fas fa-print me-1"></i>Print Receipt
        </button>
        <a href="{{ route('payments.show', $payment) }}" class="btn btn-back">Return to App</a>
    </div>

    @php
        $companySetting = \App\Models\CompanySetting::first();
        // Dynamic Amount in Words
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        $words = $f->format(floor($payment->amount));
        $decimal = round($payment->amount - floor($payment->amount), 2) * 100;
        $paise = ($decimal > 0) ? " and " . $f->format($decimal) . " Paise" : "";
        $amountWords = ($companySetting->currency_symbol ?? 'Rupees') . " " . ucwords($words) . $paise . " Only";
    @endphp

    <div class="receipt-box">
        <div class="voucher-header">
            <div class="voucher-title">Payment Receipt</div>
            <div class="meta-info">
                <table class="meta-table">
                    <tr><td class="meta-label">Voucher No</td></tr>
                    <tr><td class="meta-value">RV-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td></tr>
                    <tr><td class="meta-label">Receipt Date</td></tr>
                    <tr><td class="meta-value" style="padding-bottom: 0;">{{ $payment->payment_date->format('d/m/Y') }}</td></tr>
                </table>
            </div>
        </div>

        <div class="header-main">
            <div class="company-info">
                @if($companySetting && $companySetting->logo_path && extension_loaded('gd') && file_exists(public_path('storage/' . $companySetting->logo_path)))
                    <img src="{{ public_path('storage/' . $companySetting->logo_path) }}" alt="Logo" style="max-height: 35px; margin-bottom: 5px; display:block;">
                @endif
                <div class="company-name">{{ $companySetting->name ?? 'Company Name' }}</div>
                <div class="company-details" style="font-size: 9px; line-height: 1.2;">
                    {{ $companySetting->address ?? '' }}<br>
                    Contact: {{ $companySetting->phone ?? '' }}
                </div>
            </div>

            <div class="customer-info" style="width: 60%; text-align: right;">
                <div style="display: flex; justify-content: flex-end; gap: 25px;">
                    <div>
                        <div class="section-label">Received From:</div>
                        <div class="customer-name">{{ $payment->customer->name }}</div>
                        <div style="font-size: 10px; color: #8592a3;">{{ $payment->customer->address }}</div>
                    </div>
                    @if($payment->paymentParty)
                    <div style="text-align: right; border-left: 1px solid #eee; padding-left: 20px;">
                        <div class="section-label">Payment Party:</div>
                        <div class="customer-name" style="color: #696cff; font-weight: 800;">{{ $payment->paymentParty->name }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th style="text-align: right;">Amount Applied</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->settlements as $settlement)
                <tr>
                    <td style="font-weight: 700;">{{ $settlement->bill ? $settlement->bill->bill_number : 'O/B' }}</td>
                    <td>{{ $settlement->bill ? $settlement->bill->bill_date->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: right; font-weight: 700;">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($settlement->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="amount-container">
            <div class="amount-words">
                <div class="section-label" style="font-style: normal; margin-bottom: 2px;">In Words:</div>
                <strong>{{ $amountWords }}</strong>
            </div>
            <div class="amount-block">
                <div class="amount-label">Total Paid</div>
                <div class="amount-value">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($payment->amount, 2) }}</div>
                <div style="font-size: 9px; color: #abb8c3;">Mode: {{ strtoupper($payment->mode) }}</div>
            </div>
        </div>

        <div style="margin-bottom: 8px;">
            <div class="section-label">Reference / Remarks:</div>
            <div class="remark-box">
                {{ $payment->remarks ?: 'Payment received towards outstanding invoices.' }}
            </div>
        </div>

        <div class="signature-grid">
            <div class="signature-item" style="width: 30%; text-align: center;">
                <div style="border-top: 0.8pt solid #435971; padding-top: 4px; font-size: 9px; font-weight: 800; color: #696cff; text-transform: uppercase;">Prepared By</div>
            </div>
            <div class="signature-item" style="width: 30%; text-align: center;">
                <div style="border-top: 0.8pt solid #435971; padding-top: 4px; font-size: 9px; font-weight: 800; color: #696cff; text-transform: uppercase;">Authorized By</div>
            </div>
            <div class="signature-item" style="width: 30%; text-align: center;">
                <div style="border-top: 0.8pt solid #435971; padding-top: 4px; font-size: 9px; font-weight: 800; color: #696cff; text-transform: uppercase;">Received By</div>
            </div>
        </div>
    </div>

</body>
</html>
