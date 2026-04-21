<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan {{ $deliveryChallan->challan_number }}</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .invoice-box {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .footer {
                position: fixed;
                bottom: 0mm;
                left: 0;
                right: 0;
                text-align: center;
                background: #fff;
                border-top: 1.5pt solid #000;
                padding-top: 15px;
            }
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
            font-size: 13px;
            line-height: 1.4;
        }

        .invoice-box {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 40px 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .company-block .company-name {
            font-size: 32px;
            font-weight: 900;
            color: #000;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .company-block .company-info {
            font-size: 12px;
            color: #000;
        }

        .challan-title {
            font-size: 28px;
            font-weight: 700;
            color: #000;
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .challan-number {
            font-size: 14px;
            color: #000;
            font-weight: 600;
            text-align: right;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .meta-box {
            width: 48%;
        }

        .meta-box .box-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #000;
            border-bottom: 1px solid #000;
            margin-bottom: 8px;
            padding-bottom: 4px;
        }

        .meta-box p {
            margin: 6px 0;
            font-size: 14px;
            color: #000;
        }

        .meta-box .meta-label {
            font-weight: 700;
            color: #000;
            display: inline-block;
            width: 100px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1.5pt solid #000;
        }

        .items-table th {
            background-color: #f8f9fa;
            color: #000;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 10px;
            border-bottom: 2pt solid #000;
            border-right: 1pt solid #000;
            text-align: left;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1pt solid #000;
            border-right: 1pt solid #000;
            font-size: 14px;
            vertical-align: middle;
            color: #000;
        }

        .items-table th:last-child, .items-table td:last-child {
            border-right: none;
        }

        .total-row {
            margin-top: 15px;
            border-top: 2pt solid #000;
            padding-top: 12px;
            text-align: right;
            font-size: 18px;
            font-weight: 800;
            color: #000;
        }

        .footer {
            margin-top: 80px;
            text-align: center;
            font-size: 12px;
            color: #000;
            border-top: 1px solid #000;
            padding-top: 20px;
        }

        .clear {
            clear: both;
        }

        .print-bar {
            max-width: 900px;
            margin: 0 auto 20px;
            text-align: right;
        }

        .print-bar button {
            padding: 10px 30px;
            background: #696cff;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @php
        $companySetting = \App\Models\CompanySetting::first();
    @endphp

    <div class="print-bar no-print">
        <button onclick="window.print()">Print Challan</button>
    </div>

    <div class="invoice-box">
        <!-- Header -->
        <div class="header-row">
            <div class="company-block" style="display: flex; align-items: center; width: 75%;">
                @if(optional($companySetting)->logo_path)
                    <div style="margin-right: 25px;">
                        <img src="{{ asset('storage/' . $companySetting->logo_path) }}" alt="Logo" style="max-height: 80px; max-width: 80px; object-fit: contain;">
                    </div>
                @endif
                <div style="flex: 1;">
                    <div class="company-name">{{ $companySetting->name }}</div>
                    <div class="company-info" style="line-height: 1.4; font-size: 13px;">
                        {!! nl2br(e($companySetting->address)) !!}<br>
                        @if($companySetting->phone) Ph: {{ $companySetting->phone }} @endif
                        @if($companySetting->email) | Email: {{ $companySetting->email }} @endif
                        @if($companySetting->website) | Web: {{ $companySetting->website }} @endif
                        @if($companySetting->tax_number) <br><strong>STR / NTN: {{ $companySetting->tax_number }}</strong> @endif
                    </div>
                </div>
            </div>
            <div style="text-align: right;">
                <div class="challan-title">DELIVERY CHALLAN</div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="meta-row">
            <div class="meta-box">
                <div class="box-title">CUSTOMER</div>
                <p><span class="meta-label">Name:</span> <strong>{{ optional($deliveryChallan->customer)->name ?? 'N/A' }}</strong></p>
                <p><span class="meta-label">Address:</span> {{ optional($deliveryChallan->customer)->address ?? 'N/A' }}</p>
            </div>
            <div class="meta-box">
                <div class="box-title">CHALLAN INFO</div>
                <p><span class="meta-label">Challan No:</span> <strong>{{ $deliveryChallan->challan_number }}</strong></p>
                <p><span class="meta-label">Date:</span> {{ $deliveryChallan->challan_date->format('d/m/Y') }}</p>
                @if($deliveryChallan->vehicle_number)
                <p><span class="meta-label">Vehicle No:</span> <strong>{{ $deliveryChallan->vehicle_number }}</strong></p>
                @endif
                @if($deliveryChallan->remarks)
                <p><span class="meta-label">Remarks:</span> {{ $deliveryChallan->remarks }}</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">S. No</th>
                    <th style="width: 160px; text-align: center;">BUNDLES</th>
                    <th>ITEM DESCRIPTION</th>
                    <th style="width: 80px; text-align: center;">QTY.</th>
                    <th style="width: 120px;">REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @php $lineNo = 1; @endphp
                @foreach($deliveryChallan->items as $item)
                <tr>
                    <td style="text-align: center; color: #566a7f; vertical-align: middle;">{{ $lineNo++ }}</td>
                    <td style="text-align: center; font-weight: 500; font-size: 11px; color: #333; vertical-align: middle; padding: 5px;">{{ $item->bundles ?? '-' }}</td>
                    <td style="vertical-align: middle;">
                        <span style="font-weight: 600; font-size: 13px; color: #000; display: block;">{{ optional($item->item)->name ?? 'Product Deleted' }}</span>
                    </td>
                    <td style="text-align: center; font-weight: 600; font-size: 13px; vertical-align: middle; color: #000;">{{ number_format($item->quantity) }}</td>
                    <td style="font-size: 12px; color: #566a7f; vertical-align: middle;">{{ $item->remarks ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #fafafa; border-top: 2pt solid #000;">
                    <td colspan="3" style="text-align: center; font-weight: 700; font-size: 14px; border-right: 1pt solid #000; color: #000;">TOTAL QUANTITY:</td>
                    <td style="text-align: center; font-weight: 700; font-size: 14px; color: #000; border-right: 1pt solid #000;">{{ number_format($deliveryChallan->items->sum('quantity')) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signature Area -->
        <div style="position: absolute; bottom: 80px; left: 0; width: 100%;">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 40%; border: none; text-align: center; vertical-align: bottom;">
                        <div style="border-top: 1.5pt solid #000; padding-top: 5px; width: 90%; margin: 0 auto;">
                            <span style="font-weight: 700; font-size: 12px; text-transform: uppercase;">RECEIVER SIGNATURE WITH STAMP</span>
                        </div>
                    </td>
                    <td style="width: 20%; border: none;"></td>
                    <td style="width: 40%; border: none; text-align: center; vertical-align: bottom;">
                        <div style="margin-bottom: 30px; font-weight: 700; font-size: 13px; text-transform: uppercase;">FOR {{ $companySetting->name }}</div>
                        <div style="border-top: 1.5pt solid #000; padding-top: 5px; width: 90%; margin: 0 auto;">
                            <span style="font-weight: 700; font-size: 12px; text-transform: uppercase;">AUTHORISED SIGNATURE</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for choosing <strong>{{ $companySetting->name }}</strong>! This is a system-generated document.</p>
        </div>
    </div>
</body>
</html>
