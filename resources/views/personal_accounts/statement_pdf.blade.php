<!DOCTYPE html>
<html>
<head>
    <title>Account Statement - {{ $user->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .company { font-size: 18px; font-weight: bold; }
        .address { font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .summary { margin-bottom: 20px; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">{{ $companySetting->name }}</div>
        <div class="address">{{ $companySetting->address }}</div>
        <h2>Account Statement - {{ $user->name }}</h2>
    </div>

    @if($filters['from_date'] || $filters['to_date'])
    <div class="summary">
        <p><strong>Period:</strong> {{ $filters['from_date'] ?: 'Beginning' }} to {{ $filters['to_date'] ?: 'Present' }}</p>
    </div>
    @endif

    <div class="summary">
        <p><strong>Total Commission:</strong> ₨{{ number_format($statement['commission_total'], 2) }}</p>
        <p><strong>Total Payments:</strong> ₨{{ number_format($statement['payment_total'], 2) }}</p>
        <p><strong>Current Balance:</strong> ₨{{ number_format($statement['balance'], 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount (₨)</th>
                <th>Balance (₨)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statement['entries'] as $entry)
            <tr>
                <td>{{ $entry['display_date'] }}</td>
                <td>{{ ucfirst($entry['type']) }}</td>
                <td>
                    @if($entry['type'] === 'commission')
                        Commission {{ $entry['reference'] !== '-' ? "({$entry['reference']})" : '' }}
                        @if($entry['notes'] !== '-')
                            <br><small>{{ $entry['notes'] }}</small>
                        @endif
                    @elseif($entry['type'] === 'payment')
                        Payment {{ $entry['reference'] !== '-' ? "({$entry['reference']})" : '' }}
                        @if($entry['notes'] !== '-')
                            <br><small>{{ $entry['notes'] }}</small>
                        @endif
                    @endif
                </td>
                <td>
                    @if($entry['type'] === 'commission')
                        +{{ number_format($entry['commission_amount'], 2) }}
                    @elseif($entry['type'] === 'payment')
                        ({{ number_format($entry['payment_amount'], 2) }})
                    @endif
                </td>
                <td>{{ number_format($entry['balance'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No transactions found for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 40px;">
        <p>Generated on {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
