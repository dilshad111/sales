@extends('layouts.app')

@section('title', 'Purchase DC Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <h4 class="mb-0 text-primary fw-bold font-outfit"><i class="fas fa-file-invoice me-2"></i>DC Details: {{ $purchase_delivery_challan->challan_number }}</h4>
    <div>
        <button onclick="window.print()" class="btn btn-primary shadow-sm px-4">
            <i class="fas fa-print me-2"></i> PRINT DC
        </button>
        <a href="{{ route('purchase_delivery_challans.index') }}" class="btn btn-outline-secondary ms-2">
            <i class="fas fa-arrow-left me-1"></i> BACK
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 invoice-container" id="printableDC">
    <div class="card-body p-4 p-md-5">
        <!-- Header Branding (Optional/Generic) -->
        <div class="row align-items-center mb-4">
            <div class="col-6">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary text-white rounded p-2 me-3 d-print-none">
                        <i class="fas fa-truck-loading fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="mb-0 fw-bold text-primary display-6 font-outfit">PURCHASE DC</h2>
                    </div>
                </div>
                <div class="badge bg-label-primary px-3 py-2 fs-6">#{{ $purchase_delivery_challan->challan_number }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="mb-3">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Date of Issue</div>
                    <div class="h5 mb-0 fw-bold">{{ $purchase_delivery_challan->date->format('d M, Y') }}</div>
                </div>
                @if($purchase_delivery_challan->supplier_dc_number)
                <div>
                    <div class="text-muted small fw-bold text-uppercase mb-1">Supplier Ref #</div>
                    <div class="h6 mb-0 text-dark">{{ $purchase_delivery_challan->supplier_dc_number }}</div>
                </div>
                @endif
            </div>
        </div>

        <hr class="my-4 opacity-50">

        <!-- Information Blocks -->
        <div class="row g-4 mb-5">
            <div class="col-md-7">
                <div class="d-flex">
                    <div class="me-3">
                        <div class="bg-light rounded-circle p-2 text-primary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-2 ls-1" style="font-size: 0.7rem;">Supplier Details</h6>
                        <h5 class="fw-bold mb-1 text-dark">{{ $purchase_delivery_challan->supplier->name }}</h5>
                        <p class="text-muted small mb-1 mb-md-0" style="max-width: 300px;">
                            {{ $purchase_delivery_challan->supplier->address ?: 'No address provided' }}<br>
                            @if($purchase_delivery_challan->supplier->phone)
                            <i class="fas fa-phone-alt me-1"></i> {{ $purchase_delivery_challan->supplier->phone }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="bg-light p-3 rounded-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <h6 class="text-muted text-uppercase fw-bold mb-1 ls-1" style="font-size: 0.6rem;">Vehicle Number</h6>
                            <div class="fw-bold">{{ $purchase_delivery_challan->vehicle_number ?: 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted text-uppercase fw-bold mb-1 ls-1" style="font-size: 0.6rem;">System DC #</h6>
                            <div class="fw-bold text-primary">{{ $purchase_delivery_challan->challan_number }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle custom-print-table">
                <thead class="bg-light">
                    <tr class="text-center text-muted fw-bold" style="font-size: 0.65rem; background-color: #f8f9fa;">
                        <th style="width: 40px;">#</th>
                        <th class="text-start" style="min-width: 200px;">ITEM DESCRIPTION</th>
                        <th style="width: 80px;">QTY.</th>
                        <th style="width: 60px;">UOM</th>
                        <th style="width: 100px;">RATE</th>
                        <th style="width: 110px;">AMOUNT</th>
                        <th style="width: 80px;">TAX AMT</th>
                        <th style="width: 120px;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase_delivery_challan->items as $index => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $item->item->name }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-center text-muted">{{ $item->unit }}</td>
                        <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                        <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                        <td class="text-end">
                            <span class="text-muted" style="font-size: 0.6rem;">({{ number_format($item->tax_percent, 0) }}%)</span>
                            {{ number_format($item->tax_amount, 2) }}
                        </td>
                        <td class="text-end fw-bold text-dark">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-top-2">
                        <td colspan="7" class="text-end py-3 border-0">
                            <span class="text-muted fw-bold me-3 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Grand Total:</span>
                        </td>
                        <td class="text-end py-3 border-0">
                            <div class="text-primary fw-bold" style="font-size: 1.15rem;">
                                <small style="font-size: 0.7rem;" class="me-1">Rs.</small>{{ number_format($purchase_delivery_challan->total_amount, 2) }}
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($purchase_delivery_challan->remarks)
        <div class="mt-4 p-3 bg-light rounded-3 border-start border-primary border-4 d-print-none">
            <h6 class="text-muted text-uppercase fw-bold mb-1 ls-1" style="font-size: 0.6rem;">Special Notes / Remarks</h6>
            <div class="text-dark small">{{ $purchase_delivery_challan->remarks }}</div>
        </div>
        @endif

        <!-- Signature Blocks -->
        <div class="row mt-5 pt-5 text-center signature-section">
            <div class="col-4">
                <div class="signature-line mx-auto"></div>
                <div class="text-muted small fw-bold text-uppercase mt-2 ls-1" style="font-size: 0.6rem;">Prepared By</div>
            </div>
            <div class="col-4">
                <div class="signature-line mx-auto"></div>
                <div class="text-muted small fw-bold text-uppercase mt-2 ls-1" style="font-size: 0.6rem;">Verified By</div>
            </div>
            <div class="col-4">
                <div class="signature-line mx-auto"></div>
                <div class="text-muted small fw-bold text-uppercase mt-2 ls-1" style="font-size: 0.6rem;">Receiver Signature</div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .ls-1 { letter-spacing: 1px; }
    .signature-line {
        border-top: 1px solid #dee2e6;
        width: 80%;
    }
    .invoice-container {
        border-radius: 15px;
    }
    .custom-print-table th {
        border-top: none !important;
        padding: 8px 5px !important;
    }
    .custom-print-table td {
        font-size: 0.75rem;
        padding: 6px 5px !important;
    }
    .bg-label-primary {
        background-color: #e7e7ff !important;
        color: #696cff !important;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            background-color: #fff !important;
            padding: 0 !important;
            color: #000 !important;
        }
        .invoice-container {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .card-body {
            padding: 0 !important;
        }
        .custom-print-table td {
            font-size: 0.68rem !important;
            padding: 4px 5px !important;
            color: #000 !important;
            border-color: #000 !important;
        }
        .custom-print-table th {
            font-size: 0.6rem !important;
            background-color: #eee !important;
            color: #000 !important;
            border-color: #000 !important;
            -webkit-print-color-adjust: exact;
        }
        .text-primary, .text-muted, .text-secondary, .fw-bold {
            color: #000 !important;
        }
        .badge {
            border: 1px solid #000;
            color: #000 !important;
            background: transparent !important;
        }
        .signature-section {
            margin-top: 80px !important;
        }
        .signature-line {
            border-top: 1px solid #000 !important;
        }
    }
</style>
@endsection
