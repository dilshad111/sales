<!DOCTYPE html>
<html>
<head>
    <title>Outstanding Payments Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .company { font-size: 18px; font-weight: bold; }
        .address { font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .summary { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">{{ $companySetting->name }}</div>
        <div class="address">{{ $companySetting->address }}</div>
        <h1>Outstanding Payments Report</h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>Bill #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Outstanding</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bills as $data)
            <tr>
                <td>{{ $data['bill']->bill_number }}</td>
                <td>{{ $data['bill']->customer->name }}</td>
                <td>{{ $data['bill']->bill_date->format('d/m/Y') }}</td>
                <td>{{ number_format($data['bill']->total, 2) }}</td>
                <td>{{ number_format($data['paid'], 2) }}</td>
                <td>{{ number_format($data['outstanding'], 2) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $data['status'])) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="summary">
        <p>Total Billed: {{ number_format($summary['total_billed'], 2) }}</p>
        <p>Total Paid: {{ number_format($summary['total_paid'], 2) }}</p>
        <p>Total Outstanding: {{ number_format($summary['total_outstanding'], 2) }}</p>
    </div>
</body>
</html>
