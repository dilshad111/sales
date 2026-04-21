@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Reports /</span> Inventory Report
</h4>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Item name or code...">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-boxes me-2 text-primary"></i>Item Inventory Summary</h6>
            <div class="btn-group btn-group-sm">
                <button onclick="window.print()" class="btn btn-outline-secondary"><i class="fas fa-print"></i></button>
                {{-- Add real PDF/Excel routes here if needed, but for now matching the UI --}}
                <button class="btn btn-outline-danger disabled"><i class="fas fa-file-pdf"></i></button>
                <button class="btn btn-outline-success disabled"><i class="fas fa-file-excel"></i></button>
            </div>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Code</th>
                    <th>Item Name</th>
                    <th>Customer</th>
                    <th>UOM</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Qty Sold</th>
                    <th class="text-end">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @php $grandQty = 0; $grandRevenue = 0; @endphp
                @forelse($items as $item)
                @php
                    $grandQty += $item->total_qty_sold;
                    $grandRevenue += $item->total_revenue;
                @endphp
                <tr>
                    <td class="ps-4"><span class="badge bg-label-primary">{{ $item->code }}</span></td>
                    <td class="fw-bold">{{ $item->name }}</td>
                    <td>{{ $item->customer->name ?? '-' }}</td>
                    <td>{{ $item->uom ?? '-' }}</td>
                    <td class="text-end">{{ number_format($item->price, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($item->total_qty_sold) }}</td>
                    <td class="text-end fw-bold text-success">{{ number_format($item->total_revenue, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No items found.</td>
                </tr>
                @endforelse
            </tbody>
            @if($items->count() > 0)
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="5" class="text-end ps-4">TOTALS:</td>
                    <td class="text-end">{{ number_format($grandQty) }}</td>
                    <td class="text-end text-success">{{ number_format($grandRevenue, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<style>
@media print {
    .btn, .sidebar, .header-navbar, form { display: none !important; }
    .content-wrapper { margin: 0 !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endsection
