<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Statement - {{ $customer ? $customer->name : 'All' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .customer-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        .customer-info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #333;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            border: 1px solid #333;
        }
        table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tbody tr.payment-row {
            background-color: #e8f5e9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #28a745;
            font-weight: bold;
        }
        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }
        .footer-total {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
        }
        .summary p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CUSTOMER STATEMENT</h1>
        @if($startDate || $endDate)
        <p style="margin: 5px 0;">
            Period: 
            @if($startDate && $endDate)
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @elseif($startDate)
                From {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
            @elseif($endDate)
                Up to {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @endif
        </p>
        @endif
    </div>

    @if($customer)
    <div class="customer-info">
        <p><strong>Customer Name:</strong> {{ $customer->name }}</p>
        <p><strong>Phone:</strong> {{ $customer->phone }}</p>
        <p><strong>Email:</strong> {{ $customer->email }}</p>
        <p><strong>Address:</strong> {{ $customer->address }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">S. No.</th>
                <th style="width: 7%;" class="text-center">Date</th>
                <th style="width: 8%;" class="text-center">Bill No.</th>
                <th style="width: 30%;" class="text-center">Item Description</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 8%;" class="text-center">Rate/Each</th>
                <th style="width: 11%;" class="text-center">Sales Amount</th>
                <th style="width: 11%;" class="text-center">Payment Received</th>
                <th style="width: 11%;" class="text-center">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
            <tr class="{{ $transaction['type'] == 'payment' ? 'payment-row' : '' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}</td>
                <td class="text-center">{{ $transaction['bill_no'] }}</td>
                <td>{{ $transaction['description'] }}</td>
                <td class="text-center">
                    @if($transaction['quantity'] !== '-')
                        {{ number_format($transaction['quantity'], 0) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if($transaction['rate'] !== '-')
                        ₨{{ number_format($transaction['rate'], 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if($transaction['sales_amount'] > 0)
                        ₨{{ number_format($transaction['sales_amount'], 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right {{ $transaction['payment_received'] > 0 ? 'text-success' : '' }}">
                    @if($transaction['payment_received'] > 0)
                        ₨{{ number_format($transaction['payment_received'], 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right {{ $transaction['balance'] > 0 ? 'text-danger' : ($transaction['balance'] < 0 ? 'text-success' : '') }}">
                    ₨{{ number_format($transaction['balance'], 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No transactions found for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot>
            <tr class="footer-total">
                <td colspan="6" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>₨{{ number_format($transactions->sum('sales_amount'), 2) }}</strong></td>
                <td class="text-right text-success"><strong>₨{{ number_format($transactions->sum('payment_received'), 2) }}</strong></td>
                <td class="text-right {{ $transactions->last()['balance'] > 0 ? 'text-danger' : ($transactions->last()['balance'] < 0 ? 'text-success' : '') }}">
                    <strong>₨{{ number_format($transactions->last()['balance'], 2) }}</strong>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if($transactions->count() > 0)
    <div class="summary">
        <p><strong>Summary:</strong></p>
        @if($transactions->last()['balance'] > 0)
            <p>Outstanding balance of <strong>₨{{ number_format($transactions->last()['balance'], 2) }}</strong> is due from the customer.</p>
        @elseif($transactions->last()['balance'] < 0)
            <p>Customer has a credit balance of <strong>₨{{ number_format(abs($transactions->last()['balance']), 2) }}</strong>.</p>
        @else
            <p>Account is fully settled.</p>
        @endif
    </div>
    @endif
    @endif

    <div style="margin-top: 30px; text-align: center; font-size: 8px; color: #666;">
        <p>Generated on {{ now()->format('d/m/Y h:i A') }}</p>
    </div>
</body>
</html>
