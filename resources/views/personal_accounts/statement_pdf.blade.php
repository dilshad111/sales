<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Statement - {{ $user->name }}</title>
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
        .metrics-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .metric-box {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
            background-color: #fcfcfc;
        }
        .metric-label {
            font-size: 7pt;
            text-transform: uppercase;
            color: #7f8c8d;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .metric-value {
            font-size: 12pt;
            font-weight: bold;
        }
        .val-total { color: #2c3e50; }
        .val-paid { color: #27ae60; }
        .val-balance { color: #c0392b; }

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
            @if(extension_loaded('gd') && $companySetting->logo_path)
                <img src="{{ storage_path('app/public/' . $companySetting->logo_path) }}" class="logo">
            @endif
            <div class="company-name">{{ $companySetting->name }}</div>
            <div class="company-details">{{ $companySetting->address }}</div>
            <div class="company-details">Phone: {{ $companySetting->phone }} | Email: {{ $companySetting->email }}</div>
        </div>

        <div class="report-title-container">
            <div class="report-title">Account Statement: {{ $user->name }}</div>
        </div>

        <table class="metrics-table" style="border: none;">
            <tr>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Total Commission</div>
                    <div class="metric-value val-total">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($statement['commission_total'], 2) }}</div>
                </td>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Total Payments</div>
                    <div class="metric-value val-paid">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($statement['payment_total'], 2) }}</div>
                </td>
                <td class="metric-box" style="width: 33%;">
                    <div class="metric-label">Current Balance</div>
                    <div class="metric-value {{ $statement['balance'] > 0 ? 'val-balance' : 'val-paid' }}">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($statement['balance'], 2) }}</div>
                </td>
            </tr>
        </table>

        @if($filters['from_date'] || $filters['to_date'])
        <div style="margin-bottom: 10px; font-size: 8pt; color: #666;">
            <strong>Period:</strong> {{ $filters['from_date'] ?: 'Beginning' }} to {{ $filters['to_date'] ?: 'Present' }}
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 12%;">Type</th>
                    <th style="width: 40%;">Description</th>
                    <th style="width: 18%;" class="text-right">Transaction</th>
                    <th style="width: 18%;" class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($statement['entries'] as $entry)
                <tr>
                    <td class="text-center">{{ $entry['display_date'] }}</td>
                    <td class="text-center">{{ ucfirst($entry['type']) }}</td>
                    <td>
                        @if($entry['type'] === 'commission')
                            <strong>Commission</strong> {{ $entry['reference'] !== '-' ? "({$entry['reference']})" : '' }}
                            @if($entry['notes'] !== '-') <br><small style="color:#777;">{{ $entry['notes'] }}</small> @endif
                        @else
                            <strong>Payment</strong> {{ $entry['reference'] !== '-' ? "({$entry['reference']})" : '' }}
                            @if($entry['notes'] !== '-') <br><small style="color:#777;">{{ $entry['notes'] }}</small> @endif
                        @endif
                    </td>
                    <td class="text-right">
                        @if($entry['type'] === 'commission')
                            <span class="text-danger">+{{ number_format($entry['commission_amount'], 2) }}</span>
                        @else
                            <span class="text-success">-{{ number_format($entry['payment_amount'], 2) }}</span>
                        @endif
                    </td>
                    <td class="text-right font-weight-bold {{ $entry['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($entry['balance'], 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-right">NET CLOSING BALANCE</td>
                    <td class="text-right">-</td>
                    <td class="text-right {{ $statement['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($statement['balance'], 2) }}
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
