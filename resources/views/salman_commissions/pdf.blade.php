<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Commission Invoice #{{ $commission->id }}</title>
    <style>
        body { font-family: 'DejaVu Sans', serif; font-size: 10px; margin: 20px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #ed1c24; padding-bottom: 10px; margin-bottom: 20px; }
        h1 { margin: 0; font-size: 18px; color: #ed1c24; }
        .bill-info { width: 100%; margin-bottom: 20px; }
        .bill-info td { vertical-align: top; }
        
        table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items th, table.items td { border: 1px solid #ddd; padding: 6px; }
        table.items th { background: #f8f9fa; font-weight: bold; text-align: center; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-success { color: #28a745; }
        
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; font-size: 8px; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>COMMISSION INVOICE</h1>
        <div>Reference: #CB-{{ str_pad($commission->id, 5, '0', STR_PAD_LEFT) }}</div>
        <div>Date: {{ $commission->commission_date->format('d/m/Y') }}</div>
    </div>

    <table class="bill-info">
        <tr>
            <td style="width: 50%;">
                <div class="fw-bold" style="font-size: 11px; margin-bottom: 5px;">TO:</div>
                <div style="font-size: 11px;">{{ $commission->user->name }}</div>
                <div>{{ $commission->user->email }}</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="fw-bold" style="font-size: 11px; margin-bottom: 5px;">CUSTOMER REFERENCE:</div>
                <div style="font-size: 11px;">{{ $commission->customer->name ?? '-' }}</div>
                <div>{{ $commission->customer->address ?? '' }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 30px;">S#</th>
                <th>Item Name</th>
                <th style="width: 60px;">Bill #</th>
                <th style="width: 50px;">Qty</th>
                <th style="width: 60px;">Rate</th>
                <th style="width: 80px;">Amount</th>
                <th style="width: 40px;">%</th>
                <th style="width: 80px;">Comm. Amt</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 1; @endphp
            @foreach($commission->details as $detail)
                @foreach($detail->bill->billItems as $item)
                <tr>
                    <td class="text-center">{{ $sno++ }}</td>
                    <td>{{ $item->item->name }}</td>
                    <td class="text-center">{{ $detail->bill->bill_number }}</td>
                    <td class="text-right">{{ number_format($item->quantity) }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    <td class="text-center">{{ number_format($detail->percentage, 2) }}%</td>
                    <td class="text-right fw-bold">
                        @php $itemComm = ($item->total * $detail->percentage) / 100; @endphp
                        {{ number_format($itemComm, 2) }}
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-size: 12px; background-color: #f8f9fa;">
                <th colspan="7" class="text-right" style="padding: 10px;">Total Payable Commission:</th>
                <th class="text-right text-success" style="padding: 10px;">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($commission->amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @if($commission->notes)
    <div style="margin-top: 20px;">
        <div class="fw-bold small">REMARKS:</div>
        <div style="margin-top: 5px; color: #444;">{{ $commission->notes }}</div>
    </div>
    @endif

    <div class="footer">
        Generated on {{ now()->format('d/m/Y h:i A') }} | This is a computer generated document.
    </div>
</body>
</html>
