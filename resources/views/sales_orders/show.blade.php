@extends('layouts.app')

@section('title', 'Sales Order - ' . $salesOrder->so_number)

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice me-2 text-primary"></i>Sales Order: {{ $salesOrder->so_number }}
        </h1>
        <div class="d-print-none">
            <a href="{{ route('sales_orders.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('delivery_challans.create', ['sales_order_id' => $salesOrder->id]) }}" class="btn btn-info btn-sm">
                <i class="fas fa-truck me-1"></i> Create DC
            </a>
            <a href="{{ route('sales_orders.edit', $salesOrder) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold">{{ $salesOrder->customer->name }}</h5>
                    <p class="text-muted mb-0">{{ $salesOrder->customer->address }}</p>
                    <p class="text-muted mb-0">{{ $salesOrder->customer->phone }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 150px;">SO Number:</th>
                                    <td>{{ $salesOrder->so_number }}</td>
                                </tr>
                                <tr>
                                    <th>SO Date:</th>
                                    <td>{{ $salesOrder->so_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($salesOrder->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($salesOrder->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($salesOrder->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 150px;">PO Number:</th>
                                    <td>{{ $salesOrder->po_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>PO Date:</th>
                                    <td>{{ $salesOrder->po_date ? $salesOrder->po_date->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tax Rate:</th>
                                    <td>{{ (float)$salesOrder->tax_percent }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 50px;">S.No.</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            <th>Del. Date</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesOrder->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->item->name }}</td>
                            <td class="text-center">{{ number_format($item->quantity) }}</td>
                            <td class="text-end">₨ {{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end fw-bold">₨ {{ number_format($item->total_price, 2) }}</td>
                            <td class="text-center">{{ $item->delivery_date ? $item->delivery_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->remarks ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Subtotal:</th>
                            <th class="text-end fw-bold">₨ {{ number_format($salesOrder->total_amount, 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end text-danger">Tax ({{ (float)$salesOrder->tax_percent }}%):</th>
                            <th class="text-end text-danger fw-bold">₨ {{ number_format($salesOrder->tax_amount, 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end h5 fw-bold">Grand Total:</th>
                            <th class="text-end h5 fw-bold text-primary">₨ {{ number_format($salesOrder->grand_total, 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if($salesOrder->remarks)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Remarks</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $salesOrder->remarks }}</p>
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    .d-print-none {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        background: transparent !important;
        border-bottom: 2px solid #eee !important;
    }
    body {
        background-color: white !important;
        color: black !important;
    }
}
</style>
@endsection
