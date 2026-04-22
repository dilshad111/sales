@extends('layouts.app')

@section('title', 'Select Sales Order for DC')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice me-2 text-primary"></i>Select Sales Order
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('sales_orders.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Create Sales Order
            </a>
            <a href="{{ route('delivery_challans.create') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-plus me-1"></i> Create Direct DC
            </a>
            <a href="{{ route('delivery_challans.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('delivery_challans.select_so') }}">
                <div class="row g-3">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by SO# or Customer Name..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Orders Table -->
    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">SO Number</th>
                        <th>Customer</th>
                        <th class="text-center">SO Date</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesOrders as $order)
                    <tr>
                        <td class="ps-4">
                            <code class="fw-bold fs-6">{{ $order->so_number }}</code>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $order->customer->name }}</div>
                            <small class="text-muted">{{ $order->customer->address }}</small>
                        </td>
                        <td class="text-center">{{ $order->so_date->format('d/m/Y') }}</td>
                        <td class="text-end fw-bold">₨ {{ number_format($order->grand_total, 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('delivery_challans.create', ['sales_order_id' => $order->id]) }}" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-arrow-right me-1"></i> Select & Create DC
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">No pending sales orders found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($salesOrders->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {!! $salesOrders->links() !!}
        </div>
        @endif
    </div>
</div>
@endsection
