<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carton Costing Sheet - #{{ $costing->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 10px 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 2px 0 0;
            color: #666;
            font-size: 10px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .info-box {
            width: 48%;
        }
        .info-box h3 {
            border-bottom: 1px solid #eee;
            margin-bottom: 5px;
            padding-bottom: 2px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .section-title {
            background-color: #eee;
            padding: 3px 10px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            border-left: 4px solid #333;
            font-size: 11px;
        }
        .summary-table {
            width: 100%;
        }
        .summary-table th {
            text-align: left;
        }
        .total-row {
            font-size: 14px;
            background-color: #e9ecef;
        }
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            width: 180px;
            border-top: 1px solid #333;
            text-align: center;
            padding-top: 3px;
            font-weight: bold;
        }
        @media print {
            body { padding: 0 10px; }
            .no-print { display: none; }
            .btn-print { display: none; }
            @page {
                margin: 0.5cm;
            }
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            font-size: 12px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="btn-print">Print Sheet</button>

    <div class="header">
        <h1>Carton Costing Sheet</h1>
        <p>Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>Customer Details</h3>
            <strong>Customer:</strong> {{ $costing->customer?->name ?? 'N/A' }}<br>
            <strong>Costing ID:</strong> #{{ $costing->id }}<br>
            <strong>FEFCO Code:</strong> {{ $costing->fefco_code }}
        </div>
        <div class="info-box">
            <h3>Carton Specification</h3>
            <strong>Ply:</strong> {{ $costing->ply }} Ply<br>
            <strong>Dimensions:</strong> {{ $costing->length }} × {{ $costing->width }} × {{ $costing->height }} mm<br>
            <strong>Creator:</strong> {{ $costing->user?->name ?? 'System' }}
        </div>
    </div>

    <div class="section-title">Production Parameters</div>
    <table>
        <thead>
            <tr>
                <th>Deckle Size (Inch)</th>
                <th>Sheet Length (Inch)</th>
                <th>UPS</th>
                <th>Paper Tax (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($costing->deckle_size, 2) }}</td>
                <td>{{ number_format($costing->sheet_length_manual, 2) }}</td>
                <td>{{ $costing->ups }}</td>
                <td>{{ number_format($costing->paper_tax_rate, 2) }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Material & Layer Details</div>
    <table>
        <thead>
            <tr>
                <th>Layer Name</th>
                <th>Quality</th>
                <th class="text-end">GSM</th>
                <th class="text-end">Rate (₨/kg)</th>
                <th class="text-end">Flute</th>
                <th class="text-end">Amount (₨)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result['layers'] as $index => $layer)
            <tr>
                <td>{{ $layerLabels[$index]['label'] ?? 'Layer '.($index+1) }}</td>
                <td>{{ $layer['quality'] }}</td>
                <td class="text-end">{{ number_format($layer['gsm'], 2) }}</td>
                <td class="text-end">{{ number_format($layer['rate'], 2) }}</td>
                <td class="text-end">{{ $layer['flute'] ?? '—' }}</td>
                <td class="text-end">{{ number_format($layer['cost'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Cost Breakdown & Summary</div>
    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <table style="width: 100%;">
                <tr>
                    <th>Sheet Width</th>
                    <td>{{ number_format($result['sheet_width'], 2) }} mm</td>
                </tr>
                <tr>
                    <th>Sheet Length</th>
                    <td>{{ number_format($result['sheet_length'], 2) }} mm</td>
                </tr>
                <tr>
                    <th>Sheet Area</th>
                    <td>{{ number_format($result['sheet_area'], 4) }} m²</td>
                </tr>
            </table>
        </div>
        <div style="flex: 1;">
            <table class="summary-table" style="width: 100%;">
                <tr>
                    <th>Total Paper Cost</th>
                    <td class="text-end">₨{{ number_format($result['total_paper_cost'], 2) }}</td>
                </tr>
                <tr>
                    <th>Wastage ({{ number_format($result['wastage_rate_percent'], 2) }}%)</th>
                    <td class="text-end">₨{{ number_format($result['wastage_amount'], 2) }}</td>
                </tr>
                <tr>
                    <th>Overhead ({{ number_format($result['overhead_rate_percent'], 2) }}%)</th>
                    <td class="text-end">₨{{ number_format($result['overhead'], 2) }}</td>
                </tr>
                <tr>
                    <th>Profit ({{ number_format($result['profit_rate_percent'], 2) }}%)</th>
                    <td class="text-end">₨{{ number_format($result['profit'], 2) }}</td>
                </tr>
                <tr>
                    <th>Separator Cost</th>
                    <td class="text-end">₨{{ number_format($result['separator_cost'], 2) }}</td>
                </tr>
                <tr>
                    <th>Honeycomb Cost</th>
                    <td class="text-end">₨{{ number_format($result['honeycomb_cost'], 2) }}</td>
                </tr>
                <tr class="total-row">
                    <th>Final Carton Cost</th>
                    <td class="text-end"><strong>₨{{ number_format($result['final_carton_cost'], 2) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <div>
            <div style="height: 40px;"></div>
            <div class="signature-line">Prepared By</div>
        </div>
        <div>
            <div style="height: 40px;"></div>
            <div class="signature-line">Authorized By</div>
        </div>
    </div>

    <script>
        // Auto-print if needed
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
