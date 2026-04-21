<!DOCTYPE html>
<html>
<head>
    <title>Profit & Loss Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .section-title { background: #f8f9fa; padding: 10px; font-weight: bold; margin-top: 20px; border-bottom: 2px solid #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .text-end { text-align: right; }
        .total-row { font-weight: bold; background: #fafafa; border-top: 2px solid #ddd; }
        .net-profit { margin-top: 40px; padding: 20px; text-align: right; background: #eee; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div style="margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px;">
        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="width: 100px; border: none; vertical-align: middle;">
                    @if($companySetting->logo_path)
                        <img src="{{ public_path('storage/' . $companySetting->logo_path) }}" style="max-height: 70px;">
                    @endif
                </td>
                <td style="text-align: center; border: none; vertical-align: middle; padding-right: 100px;">
                    <h1 style="margin: 0; font-size: 26px; text-transform: uppercase; color: #1a1a1a; letter-spacing: 2px;">{{ $companySetting->name ?? 'Company Name' }}</h1>
                    <div style="font-weight: bold; margin-top: 2px; font-size: 14px; color: #444;">{{ $companySetting->address }}</div>
                    <div style="margin-top: 10px; font-size: 18px; text-transform: uppercase; font-weight: bold; color: #333;">
                        Income Statement (Profit & Loss)
                    </div>
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 5px; color: #666; font-size: 11px;">
            Period: {{ $startDate->format('d/m/Y') }} to {{ $endDate->format('d/m/Y') }}
        </div>
    </div>

    <div class="section-title">OPERATING INCOME</div>
    <table>
        @foreach($incomeAccounts as $data)
        <tr>
            <td>{{ $data->name }}</td>
            <td class="text-end">{{ number_format(abs($data->net_change), 2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>TOTAL INCOME</td>
            <td class="text-end">{{ number_format(abs($totalIncome), 2) }}</td>
        </tr>
    </table>

    <div class="section-title">OPERATING EXPENSES</div>
    <table>
        @foreach($expenseAccounts as $data)
        <tr>
            <td>{{ $data->name }}</td>
            <td class="text-end">{{ number_format($data->net_change, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>TOTAL EXPENSES</td>
            <td class="text-end">({{ number_format($totalExpense, 2) }})</td>
        </tr>
    </table>

    <div class="net-profit">
        {{ $netProfit >= 0 ? 'NET PROFIT' : 'NET LOSS' }}: {{ number_format(abs($netProfit), 2) }}
    </div>

</body>
</html>
