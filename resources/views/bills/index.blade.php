@extends('layouts.app')

@section('title', 'Sale Invoices')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Sale Invoices</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('delivery_challans.index', ['status' => 'pending']) }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-truck-loading fa-sm text-white-50 me-1"></i>Create Invoice via DC
        </a>
        <a href="{{ route('bills.create') }}" class="btn btn-outline-primary shadow-sm bg-white">
            <i class="fas fa-plus fa-sm me-1"></i>Direct Sale Invoice
        </a>
    </div>
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
                    <label class="form-label small fw-bold text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Show</label>
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 Records</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Records</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Records</option>
                    </select>
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

<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-3">Invoice #</th>
                        <th>Customer</th>
                        <th class="text-center">Date</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    <tr>
                        <td class="ps-3"><code class="text-primary fw-bold">{{ $bill->bill_number }}</code></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $bill->customer ? $bill->customer->name : 'Customer Deleted' }}</div>
                            <div class="small text-muted">{{ optional($bill->customer)->city }}</div>
                        </td>
                        <td class="text-center">{{ $bill->bill_date->format('d/m/Y') }}</td>
                        <td class="text-end fw-bold">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bill->total, 2) }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('bills.show', $bill) }}" class="btn btn-outline-info" title="View Bill">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('bills.edit', $bill) }}" class="btn btn-outline-warning" title="Edit Bill">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('bills.destroy', $bill) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete Bill">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No bills found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($bills->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">Page Total:</td>
                        <td class="text-end">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($bills->sum('total'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @if($bills->hasPages())
    <div class="card-footer bg-white">
        {{ $bills->links() }}
    </div>
    @endif
</div>
@endsection
