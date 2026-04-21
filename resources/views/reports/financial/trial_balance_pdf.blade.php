<!DOCTYPE html>
<html>
<head>
    <title>Trial Balance</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
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
                        Trial Balance
                    </div>
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 5px; color: #666; font-size: 11px;">
            As of {{ $date->format('d F Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Account Name</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDebit = 0; $totalCredit = 0; @endphp
            @foreach($accounts as $data)
                @php $totalDebit += $data['debit']; $totalCredit += $data['credit']; @endphp
                <tr>
                    <td>{{ $data['account']->code }}</td>
                    <td>{{ $data['account']->name }}</td>
                    <td class="text-end">{{ $data['debit'] > 0 ? number_format($data['debit'], 2) : '-' }}</td>
                    <td class="text-end">{{ $data['credit'] > 0 ? number_format($data['credit'], 2) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="font-weight: bold; background: #eee;">
            <tr>
                <td colspan="2" class="text-end">TOTAL</td>
                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Printed on {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
