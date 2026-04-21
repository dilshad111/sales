<!DOCTYPE html>
<html>
<head>
    <title>Balance Sheet</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .row { display: block; width: 100%; clear: both; }
        .col { width: 48%; float: left; }
        .col-right { float: right; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 5px; border-bottom: 1px solid #eee; }
        .text-end { text-align: right; }
        .section-header { font-weight: bold; background: #eee; padding: 5px; margin-bottom: 5px; }
        .total-box { background: #333; color: #fff; padding: 10px; text-align: right; font-weight: bold; margin-top: 10px; }
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
                        Statement of Financial Position (Balance Sheet)
                    </div>
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 5px; color: #666; font-size: 11px;">
            As of {{ $date->format('d/m/Y') }}
        </div>
    </div>

    <div class="row">
        <!-- Assets -->
        <div class="col">
            <div class="section-header">ASSETS</div>
            <table>
                @php $totalAssets = 0; @endphp
                @foreach($assets as $a)
                    @php $totalAssets += $a['balance']; @endphp
                    <tr>
                        <td>{{ $a['name'] }}</td>
                        <td class="text-end">{{ number_format($a['balance'], 2) }}</td>
                    </tr>
                @endforeach
            </table>
            <div class="text-end" style="font-weight: bold;">TOTAL ASSETS: {{ number_format($totalAssets, 2) }}</div>
        </div>

        <!-- Liabilities & Equity -->
        <div class="col col-right">
            <div class="section-header">LIABILITIES</div>
            <table>
                @php $totalLiabilities = 0; @endphp
                @foreach($liabilities as $l)
                    @php $totalLiabilities += abs($l['balance']); @endphp
                    <tr>
                        <td>{{ $l['name'] }}</td>
                        <td class="text-end">{{ number_format(abs($l['balance']), 2) }}</td>
                    </tr>
                @endforeach
            </table>
            <div class="text-end" style="font-weight: bold;">TOTAL LIABILITIES: {{ number_format($totalLiabilities, 2) }}</div>

            <div class="section-header" style="margin-top: 20px;">EQUITY</div>
            <table>
                @php $totalEquity = 0; @endphp
                @foreach($equity as $e)
                    @php $totalEquity += abs($e['balance']); @endphp
                    <tr>
                        <td>{{ $e['name'] }}</td>
                        <td class="text-end">{{ number_format(abs($e['balance']), 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td>Retained Earnings</td>
                    <td class="text-end">{{ number_format($retainedEarnings, 2) }}</td>
                </tr>
            </table>
            <div class="text-end" style="font-weight: bold;">TOTAL EQUITY: {{ number_format($totalEquity + $retainedEarnings, 2) }}</div>

            <div class="total-box">
                TOTAL LIAB. & EQUITY: {{ number_format($totalLiabilities + $totalEquity + $retainedEarnings, 2) }}
            </div>
        </div>
    </div>
</body>
</html>
