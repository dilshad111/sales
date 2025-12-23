@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-hand-holding-usd me-2 text-success"></i>Payments</h1>
    <a href="{{ route('payments.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 me-1"></i>Record Payment
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
                    <label class="form-label small fw-bold text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
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
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Customer</th>
                        <th>Bill #</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Mode</th>
                        <th>Payment Party</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="ps-3"><span class="badge bg-light text-dark border">#{{ $payment->id }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $payment->customer->name }}</div>
                            <div class="small text-muted">{{ optional($payment->customer)->phone }}</div>
                        </td>
                        <td>
                            <a href="{{ route('bills.show', $payment->bill_id) }}" class="text-decoration-none small fw-bold">
                                {{ $payment->bill->bill_number }}
                            </a>
                        </td>
                        <td class="text-end fw-bold text-success">Rs. {{ number_format($payment->amount, 2) }}</td>
                        <td class="text-center small">{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $payment->mode == 'cash' ? 'success' : 'info' }} rounded-pill px-3">
                                {{ strtoupper($payment->mode) }}
                            </span>
                        </td>
                        <td>{{ $payment->paymentParty->name ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-warning" title="Edit Payment">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete Payment">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No payment records found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-white">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
