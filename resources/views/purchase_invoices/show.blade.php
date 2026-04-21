@extends('layouts.app')

@section('title', 'Purchase Invoice ' . $purchaseInvoice->invoice_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Purchases /</span> Invoice Details
    </h4>
    <div class="btn-group">
        <a href="{{ route('purchase_invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <button type="button" class="btn btn-primary" onclick="window.print();">
            <i class="fas fa-print me-1"></i> Print
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Invoice Info -->
    <div class="col-md-8">
        <div class="card shadow-none border mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                <h6 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i>Invoice Breakdown</h6>
                <span class="badge bg-label-{{ $purchaseInvoice->status === 'pending' ? 'warning' : 'success' }} text-uppercase">
                    {{ str_replace('_', ' ', $purchaseInvoice->status) }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>Item Description</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end pe-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseInvoice->items as $index => $item)
                        <tr>
                            <td class="ps-3">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->item->name ?? 'Deleted Item' }}</div>
                                @if($item->remarks)
                                    <small class="text-muted">{{ $item->remarks }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-end text-muted">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end fw-bold pe-3 text-primary">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light border-top">
                <div class="row">
                    <div class="col-md-7">
                        @if($purchaseInvoice->notes)
                            <div class="p-3 border rounded bg-white">
                                <small class="text-muted text-uppercase fw-bold d-block mb-1">Internal Notes</small>
                                <p class="mb-0 small">{{ $purchaseInvoice->notes }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Gross Amount:</span>
                            <span class="fw-bold">{{ number_format($purchaseInvoice->gross_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax ({{ $purchaseInvoice->tax_percentage }}%):</span>
                            <span class="text-danger fw-bold">+ {{ number_format($purchaseInvoice->tax_amount, 2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">Net Payable:</span>
                            <span class="fw-bold fs-4 text-primary">Rs. {{ number_format($purchaseInvoice->net_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3 pt-2 border-top text-info small">
                            <span>Agent Comm. ({{ $purchaseInvoice->commission_percentage }}%):</span>
                            <span>{{ number_format($purchaseInvoice->commission_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($purchaseInvoice->recoveries->count() > 0)
        <div class="card shadow-none border">
            <div class="card-header bg-label-info">
                <h6 class="mb-0 fw-bold"><i class="fas fa-hand-holding-usd me-2"></i>Linked Recoveries</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseInvoice->recoveries as $recovery)
                        <tr>
                            <td>{{ $recovery->date->format('d/m/Y') }}</td>
                            <td>{{ $recovery->recovery_number }}</td>
                            <td class="text-end fw-bold">{{ number_format($recovery->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="col-md-4">
        <div class="card shadow-none border mb-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-3 fw-bold">Invoice Details</h6>
                <div class="mb-3">
                    <label class="text-muted d-block small">Invoice Number</label>
                    <span class="fw-bold fs-5 text-primary">{{ $purchaseInvoice->invoice_number }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted d-block small">Supplier Inv #</label>
                    <span class="badge bg-label-secondary fs-6">{{ $purchaseInvoice->supplier_invoice_number ?? 'N/A' }}</span>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="text-muted d-block small">Posting Date</label>
                        <span class="fw-bold text-dark">{{ $purchaseInvoice->posting_date->format('d M, Y') }}</span>
                    </div>
                    <div class="col-6">
                        <label class="text-muted d-block small">Invoice Date</label>
                        <span class="fw-bold text-dark">{{ $purchaseInvoice->date->format('d M, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-none border mb-4 border-left-primary">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-3 fw-bold">Supplier (Party)</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-3 bg-label-primary rounded p-2">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $purchaseInvoice->supplier->name }}</h6>
                        <small class="text-muted">Supplier ID: #{{ $purchaseInvoice->supplier->id }}</small>
                    </div>
                </div>
                @if($purchaseInvoice->supplier->phone)
                <div class="mb-2">
                    <i class="fas fa-phone-alt me-2 text-muted small"></i>
                    <span class="small">{{ $purchaseInvoice->supplier->phone }}</span>
                </div>
                @endif
                @if($purchaseInvoice->supplier->email)
                <div class="mb-2">
                    <i class="fas fa-envelope me-2 text-muted small"></i>
                    <span class="small">{{ $purchaseInvoice->supplier->email }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($purchaseInvoice->agent)
        <div class="card shadow-none border mb-4 border-left-info">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-3 fw-bold">Handling Agent</h6>
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3 bg-label-info rounded p-2">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $purchaseInvoice->agent->name }}</h6>
                        <small class="text-info">{{ $purchaseInvoice->commission_percentage }}% Commission Rate</small>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-outline-secondary d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <span class="small">This is a Direct Purchase (No Agent).</span>
        </div>
        @endif

        <div class="card shadow-none border bg-light">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center small mt-1">
                    <span class="text-muted">Created By:</span>
                    <span class="fw-bold text-dark">{{ $purchaseInvoice->creator->name ?? 'System' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center small mt-1">
                    <span class="text-muted">Created At:</span>
                    <span class="text-dark">{{ $purchaseInvoice->created_at->format('d/m/Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn-group, .main-menu, .navbar, .footer, .content-footer {
        display: none !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    .content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endsection
