<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Statement Report</title>
    <style>
        body { font-family: 'DejaVu Sans', serif; font-size: 10px; margin: 20px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #ed1c24; padding-bottom: 10px; margin-bottom: 20px; }
        h1 { margin: 0; font-size: 18px; color: #ed1c24; }
        .period { font-size: 11px; margin-top: 5px; }
        
        .customer-section { margin-bottom: 25px; border: 1px solid #ddd; }
        .customer-header { background: #343a40; color: white; padding: 5px 10px; font-weight: bold; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f8f9fa; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-light { background: #f8f9fa; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #ed1c24; }
        .text-success { color: #28a745; }
        
        .summary-section { margin-top: 30px; page-break-inside: avoid; }
        .summary-title { background: #007bff; color: white; padding: 5px 10px; font-weight: bold; margin-bottom: 10px; font-size: 12px; }
        .summary-table { width: 100%; }
        .summary-table td { padding: 8px; border: none; border-bottom: 1px solid #eee; }
        .col-half { width: 50%; float: left; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CASH STATEMENT REPORT</h1>
        <div class="period">
            Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'All Time' }} 
            to 
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Present' }}
        </div>
    </div>

    @foreach($reports as $report)
    <div class="customer-section">
        <div class="customer-header clearfix">
            <span style="float: left;">Customer: {{ $report['customer']->name }}</span>
            <span style="float: right;">Contact: {{ $report['customer']->phone }}</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">S.No</th>
                    <th style="width: 70px;">Date</th>
                    <th style="width: 80px;">Mode</th>
                    <th>Description</th>
                    <th style="width: 90px;">Party</th>
                    <th style="width: 90px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['payments'] as $idx => $payment)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ ucfirst($payment->mode) }}</td>
                    <td>{{ $payment->remarks ?: '-' }}</td>
                    <td class="text-center">{{ $payment->paymentParty->name ?? '-' }}</td>
                    <td class="text-right">₨{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #888; padding: 10px;">No payments received in this period.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-light">
                    <td colspan="5" class="text-right fw-bold">Sub Total:</td>
                    <td class="text-right fw-bold">₨{{ number_format($report['subtotal'], 2) }}</td>
                </tr>
                <tr class="text-danger">
                    <td colspan="5" class="text-right fw-bold">Outstanding:</td>
                    <td class="text-right fw-bold">₨{{ number_format($report['outstanding'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach

    <div class="summary-section">
        <div class="summary-title">SUMMARY</div>
        <div class="clearfix">
            <div class="col-half" style="border-right: 1px solid #ddd; height: 150px;">
                <table class="summary-table">
                    <tr>
                        <td>Received Total Amount:</td>
                        <td class="text-right fw-bold text-success">₨{{ number_format($totalReceived, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Amount Receivable:</td>
                        <td class="text-right fw-bold text-danger">₨{{ number_format($totalReceivable, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-half" style="padding-left: 20px;">
                <table class="summary-table">
                    @foreach($partySummary as $pSummary)
                    <tr>
                        <td>{{ $pSummary['name'] }}:</td>
                        <td class="text-right fw-bold">
                            @if($pSummary['amount'] > 0)
                                ₨{{ number_format($pSummary['amount'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 40px; text-align: center; color: #777; font-size: 8px; border-top: 1px solid #eee; padding-top: 10px;">
        Generated on {{ now()->format('d/m/Y h:i A') }}
    </div>
</body>
</html>
