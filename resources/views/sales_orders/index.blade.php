@extends('layouts.app')

@section('title', 'Sales Orders')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice me-2 text-primary"></i>Sales Orders</h1>
    <a href="{{ route('sales_orders.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 me-1"></i>Create Sales Order
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Customer</label>
                    <select name="customer_id" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-header bg-white py-3">
        <span class="fw-bold text-muted small"><i class="fas fa-list me-1"></i>SALES ORDER LIST</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light text-center">
                    <tr>
                        <th>SO #</th>
                        <th>PO #</th>
                        <th>Customer</th>
                        <th>SO Date</th>
                        <th>PO Date</th>
                        <th class="text-end">Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="text-center"><code class="text-primary fw-bold">{{ $order->so_number }}</code></td>
                        <td class="text-center">{{ $order->po_number ?? '-' }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $order->customer ? $order->customer->name : 'N/A' }}</div>
                        </td>
                        <td class="text-center">{{ $order->so_date ? $order->so_date->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">{{ $order->po_date ? $order->po_date->format('d/m/Y') : '-' }}</td>
                        <td class="text-end fw-bold text-primary">₨ {{ number_format($order->grand_total, 2) }}</td>
                        <td class="text-center">
                            @if($order->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($order->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('delivery_challans.create', ['sales_order_id' => $order->id]) }}" class="btn btn-info btn-sm btn-icon" title="Create DC">
                                    <i class="fas fa-truck"></i>
                                </a>
                                <a href="{{ route('sales_orders.show', $order) }}" class="btn btn-primary btn-sm btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sales_orders.edit', $order) }}" class="btn btn-warning btn-sm btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sales_orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this sales order?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No sales orders found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this Sales Order?')) {
        const form = document.getElementById('deleteForm');
        form.action = url;
        form.submit();
    }
}
</script>
@endsection
