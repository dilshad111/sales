<!DOCTYPE html>
<html>
<head>
    <title>Bill {{ $bill->bill_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 5mm;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        .company {
            font-size: 28px;
            font-weight: bold;
        }
        .address {
            font-size: 16px;
        }
        hr {
            border: 0;
            border-top: 1px solid #000;
            margin: 10px auto;
        }
        .details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: left;
        }
        thead th {
            font-weight: bold;
            text-align: center;
        }
        tbody td {
            font-size: 12.825px;
        }
        .qty-cell,
        .price-cell {
            font-size: 15px;
            font-weight: bold;
            text-align: right;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total {
            font-weight: bold;
        }
        .total-amount-cell {
            font-size: 16.2px;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            left: 6mm;
            right: 3mm;
            bottom: 3mm;
            font-size: 11px;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
    @php
        $billItems = $bill->billItems;
        $totalQuantity = $billItems->sum('quantity');
        $totalAmount = $billItems->sum('total');
    @endphp
    <div class="header">
        <div class="company">{{ $companySetting->name }}</div>
        <div class="address">{{ $companySetting->address }}</div>
        <hr>
        <h2 style="margin: 5px 0;">Sales Bill</h2>
    </div>
    <div class="details">
        <table style="border: none; margin-bottom: 5px;">
            <tr>
                <td style="border: none; padding: 0;">
                    <span style="font-size: 18px; font-weight: bold;">Bill #:</span>
                    <span style="font-size: 17px;">{{ $bill->bill_number }}</span>
                </td>
                <td style="border: none; padding: 0;" class="text-right">
                    <span style="font-size: 18px; font-weight: bold;">Date:</span>
                    <span style="font-size: 17px;">{{ $bill->bill_date->format('d/m/Y') }}</span>
                </td>
            </tr>
        </table>
        <p style="margin: 0;"><strong>Customer:</strong> {{ optional($bill->customer)->name ?? 'Customer Deleted' }}</p>
        <p style="margin: 0 0 10px 0;">{{ optional($bill->customer)->address ?? 'Address unavailable' }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 17px;">S.No.</th>
                <th style="width: 275px;">Item Name</th>
                <th style="width: 70px;">Delivery Date</th>
                <th style="width: 50px;">Quantity</th>
                <th style="width: 24px;">Price/each</th>
                <th style="width: 80px;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($billItems as $item)
            <tr>
                <td class="text-center" style="font-style: italic;font-size: 17px;">{{ $loop->iteration }}</td>
                <td>
                    <div>{{ optional($item->item)->name ?? 'Item Deleted' }}</div>
                    @if(!empty($item->remarks))
                        <div style="margin-top: 3px; font-size: 14px; color: #000000ff;"> {{ $item->remarks }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $item->delivery_date ? \Carbon\Carbon::parse($item->delivery_date)->format('d/m/Y') : '-' }}</td>
                <td class="qty-cell">{{ number_format($item->quantity) }}</td>
                <td class="price-cell">{{ number_format($item->price, 2) }}</td>
                <td class="text-right total-amount-cell">{{ number_format($item->total, 2) }}</td>
            
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-center">Subtotal</th>
                <th class="text-center">{{ number_format($totalQuantity) }}</th>
                <th class="text-center">—</th>
                <th class="text-right">{{ number_format($totalAmount, 2) }}</th>
            </tr>
        </tfoot>
    </table>
    <div class="footer">
        This is a system-generated invoice and does not require a signature.
    </div>
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('if ($PAGE_COUNT > 1) { $font = $fontMetrics->get_font("Arial", "normal"); $size = 10; $text = "Page $PAGE_NUM of $PAGE_COUNT"; $width = $pdf->get_width(); $pdf->text($width - 120, $pdf->get_height() - 15, $text, $font, $size); }');
        }
    </script>
</body>
</html>
