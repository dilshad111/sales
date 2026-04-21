<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ledger Statement - {{ $account->name }}</title>
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
        .account-info-box {
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-left: 4px solid #2c3e50;
        }
        .account-info-box p {
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
        .text-right { text-align: right; white-space: nowrap; }
        .text-center { text-align: center; white-space: nowrap; }
        .text-success { color: #2e7d32; }
        .text-danger { color: #c62828; }
        .opening-row { background-color: #e3f2fd !important; font-weight: bold; }
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
        @php $companySetting = \App\Models\CompanySetting::first(); @endphp
        <div class="header">
            @if(extension_loaded('gd') && $companySetting->logo_path)
                <img src="{{ storage_path('app/public/' . $companySetting->logo_path) }}" class="logo">
            @endif
            <div class="company-name">{{ $companySetting->name }}</div>
            <div class="company-details">{{ $companySetting->address }}</div>
            <div class="company-details">Phone: {{ $companySetting->phone }} | Email: {{ $companySetting->email }}</div>
        </div>

        <div class="report-title-container">
            <div class="report-title">Ledger Statement</div>
        </div>

        <div class="account-info-box">
            <p><strong>Account:</strong> {{ $account->name }} ({{ ucfirst($account->type) }})</p>
            <p><strong>Period:</strong> {{ $startDate ? $startDate->format('d/m/Y') : 'Beginning' }} to {{ $endDate->format('d/m/Y') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Date</th>
                    <th style="width: 10%;">TXN #</th>
                    <th style="width: 25%;">Type / Narration</th>
                    <th style="width: 15%;">Related Party</th>
                    <th style="width: 13%;" class="text-right">Debit</th>
                    <th style="width: 13%;" class="text-right">Credit</th>
                    <th style="width: 14%;" class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr class="opening-row">
                    <td class="text-center">{{ $startDate ? $startDate->format('d/m/Y') : $account->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">OP-BAL</td>
                    <td colspan="2">Opening Balance</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right {{ $openingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($openingBalance, 2) }}
                    </td>
                </tr>

                @php $currentBalance = $openingBalance; @endphp
                @foreach($entries as $entry)
                    @php $currentBalance += ($entry->debit - $entry->credit); @endphp
                    <tr>
                        <td class="text-center">{{ $entry->transaction->date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $entry->transaction->transaction_number }}</td>
                        <td>
                            <div style="font-weight: bold;">{{ $entry->transaction->formatted_type }}</div>
                            <div style="font-size: 7.5pt; color: #777;">{{ $entry->transaction->narration }}</div>
                        </td>
                        <td class="text-center">
                            @php $other = $entry->transaction->entries->where('account_id', '!=', $account->id)->first(); @endphp
                            {{ $other->account->name ?? '-' }}
                        </td>
                        <td class="text-right {{ $entry->debit > 0 ? 'text-success font-weight-bold' : '' }}">
                            {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                        </td>
                        <td class="text-right {{ $entry->credit > 0 ? 'text-danger font-weight-bold' : '' }}">
                            {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                        </td>
                        <td class="text-right font-weight-bold {{ $currentBalance >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($currentBalance, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right text-uppercase">Closing / Net:</td>
                    <td class="text-right text-success">{{ number_format($entries->sum('debit'), 2) }}</td>
                    <td class="text-right text-danger">{{ number_format($entries->sum('credit'), 2) }}</td>
                    <td class="text-right {{ $currentBalance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($currentBalance, 2) }}
                    </td>
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
