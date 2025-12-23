<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
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
        <h1>Sales Report</h1>
    </div>
    <div class="summary">
        <p>Total Sales: {{ number_format($totalSales, 2) }}</p>
        <p>Total Payments Received: {{ number_format($totalPayments, 2) }}</p>
        <p>Total Outstanding: {{ number_format($totalOutstanding, 2) }}</p>
    </div>
    <h2>Item-wise Sales</h2>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity Sold</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($itemSales as $itemData)
            <tr>
                <td>{{ $itemData['item']->name }}</td>
                <td>{{ $itemData['quantity'] }}</td>
                <td>{{ number_format($itemData['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <h2>Bill Details</h2>
    <table>
        <thead>
            <tr>
                <th>Bill #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bills as $bill)
            <tr>
                <td>{{ $bill->bill_number }}</td>
                <td>{{ $bill->customer->name }}</td>
                <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
                <td>{{ number_format($bill->total, 2) }}</td>
                <td>{{ number_format($bill->payments->sum('amount'), 2) }}</td>
                <td>{{ number_format($bill->total - $bill->payments->sum('amount'), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
