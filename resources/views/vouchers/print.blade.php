<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $voucher->formatted_type }} - {{ $voucher->transaction_number }}</title>
    <style>
        @page { 
            size: A4 portrait; 
            margin: 15mm; 
        }
        
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            font-size: 11px; 
            color: #000; 
            line-height: 1.2; 
            margin: 0; 
            padding: 0; 
            background: #fff; 
        }
        
        .container { 
            width: 170mm; 
            height: 120mm; 
            margin: 0 auto;
            padding: 5mm 8mm; 
            position: relative; 
            box-sizing: border-box;
            background: #fff;
            border: 0.1mm solid #000;
        }

        /* Use table for header for robust alignment */
        .full-width-table { width: 100%; border-collapse: collapse; }
        
        .company-header-table { width: 100%; margin-bottom: 5px; }
        .company-name { font-size: 26px; font-weight: bold; margin-bottom: 0; letter-spacing: 1px; }
        .company-address { font-size: 11px; line-height: 1.2; }
        
        .logo-box { width: 30mm; height: 30mm; border: none; text-align: left; vertical-align: top; }
        .logo-box img { width: 30mm; height: 30mm; object-fit: contain; }

        .title-section { text-align: center; margin: 3px 0; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 0; background-color: #f2f2f2; }
        .receipt-title { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; }
        
        .meta-table { width: 100%; margin-bottom: 5px; border-bottom: 0.5px solid #000; }
        .meta-cell { font-size: 11px; font-weight: bold; padding: 4px 0; }

        .parties-table { width: 100%; margin-bottom: 8px; border-bottom: 1px solid #000; }
        .party-cell { width: 50%; vertical-align: top; padding-bottom: 5px; }
        .party-label { font-size: 8px; font-weight: bold; text-transform: uppercase; margin-bottom: 1px; }
        .party-name { font-size: 12px; font-weight: bold; display: block; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; table-layout: fixed; }
        .items-table th { border-bottom: 1.5px solid #000; padding: 4px; text-align: left; text-transform: uppercase; font-size: 9px; background-color: #f2f2f2; }
        .items-table td { padding: 6px 4px; font-size: 11px; vertical-align: top; border-bottom: 0.1px solid #eee; }
        .text-right { text-align: right; }

        .summary-row-table { width: 100%; margin-top: 10px; }
        .summary-box { border: 1px solid #000; padding: 5px; width: 62%; vertical-align: top; }
        .words-label { font-size: 8px; font-weight: bold; text-transform: uppercase; display: block; }
        .words-value { font-weight: bold; font-size: 11px; font-style: italic; }
        
        .total-box { text-align: right; vertical-align: top; width: 38%; }
        .total-wrapper { border-bottom: 2px solid #000; display: inline-block; padding-bottom: 2px; }
        .total-amount { font-size: 24px; font-weight: bold; margin: 0; padding: 0; }
        .payment-mode { font-size: 9px; font-weight: bold; text-transform: uppercase; margin-top: 2px; display: block; }

        .signature-table { width: 100%; }
        .signature-table td { width: 33.33%; text-align: center; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #000; width: 80%; margin: 0 auto 4px; }
        .signature-label { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #000; }

        .print-footer { position: absolute; bottom: 2mm; width: 100%; left: 0; font-size: 7px; text-align: center; opacity: 0.5; }
        
        @media print {
            body { padding: 0; margin: 0; }
            .container { border: 0.1mm solid #000; }
        }
    </style>
</head>
<body>
    @php
        $company = \App\Models\CompanySetting::first();
        
        // Formatted type for display
        $title = match($voucher->type) {
            'PV' => 'PAYMENT VOUCHER',
            'RV' => 'PAYMENT RECEIPT',
            'JV' => 'JOURNAL VOUCHER',
            default => $voucher->formatted_type
        };

        // Convert logo to base64 to ensure it displays in all environments
        $logoBase64 = '';
        if ($company->logo_path) {
            $path = storage_path('app/public/' . $company->logo_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
    @endphp

    <div class="container">
        <!-- HEADER TABLE -->
        <table class="company-header-table">
            <tr>
                <td class="logo-box">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo">
                    @else
                        <div style="font-size: 8px; color: #ccc;">LOGO</div>
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <div class="company-name text-uppercase" style="font-size: 38px; letter-spacing: 2px; color: #000;">{{ $company->name ?? config('app.name') }}</div>
                    <div class="company-address" style="font-size: 13px; font-weight: bold;">
                        {{ $company->address ?? 'Address not set.' }}
                        @if($company->phone) <br>Contact: {{ $company->phone }} @endif
                        @if($company->email) | Email: {{ $company->email }} @endif
                    </div>
                </td>
                <td style="width: 23mm;"></td> <!-- Spacer for balance -->
            </tr>
        </table>

        <div class="title-section">
            <h1 class="receipt-title">{{ $title }}</h1>
        </div>

        <!-- META ROW (Single Line) -->
        <table class="meta-table">
            <tr>
                <td class="meta-cell" style="text-align: left; padding-left: 5px; width: 35%;">
                    VOUCHER NO: <span style="font-size: 12px;">{{ $voucher->transaction_number }}</span>
                </td>
                <td class="meta-cell" style="text-align: center; width: 30%;">
                    <div style="background: #000; color: #fff; display: inline-block; padding: 2px 10px; border-radius: 2px; text-transform: uppercase; font-size: 9px;">
                        Payment Mode: <span style="font-weight: bold;">{{ $voucher->payment_mode ?: 'N/A' }}</span>
                    </div>
                </td>
                <td class="meta-cell" style="text-align: right; padding-right: 15px; width: 35%;">
                    DATE: <span style="font-size: 12px;">{{ $voucher->date->format('d/m/Y') }}</span>
                </td>
            </tr>
        </table>

        <!-- PARTIES TABLE -->
        <table class="parties-table">
            <tr>
                <td class="party-cell" style="padding-left: 5px;">
                    <span class="party-label">
                        {{ $voucher->type == 'RV' ? 'Received From:' : ($voucher->type == 'PV' ? 'Paid To:' : 'Debit Account:') }}
                    </span>
                    <span class="party-name">
                        @php
                            $debitParty = $voucher->entries->where('debit', '>', 0)->first();
                            echo $debitParty ? $debitParty->account->name : 'N/A';
                        @endphp
                    </span>
                </td>
                <td class="party-cell" style="text-align: right; padding-right: 15px;">
                    <span class="party-label">
                        {{ $voucher->type == 'JV' ? 'Credit Account:' : 'Payment Account:' }}
                    </span>
                    <span class="party-name">
                        @php
                            $creditParty = $voucher->entries->where('credit', '>', 0)->first();
                            echo $creditParty ? $creditParty->account->name : 'N/A';
                        @endphp
                    </span>
                </td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <div style="padding: 0 5px;">
            <table class="items-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 72%;">PARTICULARS / NARRATION</th>
                        <th class="text-right" style="width: 28%; padding-right: 25px;">AMOUNT (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $displayEntries = $voucher->entries->filter(function($e) use ($voucher) {
                            if($voucher->type == 'RV') return $e->credit > 0;
                            if($voucher->type == 'PV') return $e->debit > 0;
                            return true;
                        });
                    @endphp
                    @foreach($displayEntries as $entry)
                    <tr>
                        <td style="font-size: 10px;">{{ $entry->narration ?: $voucher->narration ?: 'Transaction entry' }}</td>
                        <td class="text-right" style="font-weight: bold; padding-right: 25px;">
                            {{ number_format($entry->debit + $entry->credit, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- SUMMARY & TOTAL -->
        <div style="padding: 0 5px; margin-top: 10px;">
            <table class="summary-row-table">
                <tr>
                    <td class="summary-box">
                        <span class="words-label">AMOUNT IN WORDS:</span>
                        <span class="words-value">{{ $voucher->amount_in_words() }}</span>
                    </td>
                    <td class="total-box" style="padding-right: 20px;">
                        <div class="total-wrapper">
                            <span class="total-amount">{{ $company->currency_symbol ?? 'Rs.' }} {{ number_format($voucher->total_amount, 2) }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- SIGNATURES -->
        <div style="position: absolute; bottom: 4mm; left: 8mm; right: 8mm;">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">Prepared By</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">Authorized By</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">Received By</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
