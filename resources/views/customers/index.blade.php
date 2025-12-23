@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Finance /</span> Customers
    </h4>
    <a href="{{ route('customers.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="fas fa-plus me-1"></i> Add Customer
    </a>
</div>

<!-- Search Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-9">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Customers List Card -->
<div class="card shadow-none border">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Customer Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Opening Balance</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($customers as $customer)
                <tr>
                    <td class="ps-4">
                        <span class="fw-bold text-dark">{{ $customer->name }}</span>
                    </td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->email }}</td>
                    <td class="text-center">
                        <span class="badge bg-label-{{ $customer->status == 'active' ? 'success' : 'danger' }} rounded-pill">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <span class="fw-semibold {{ $customer->opening_balance >= 0 ? 'text-danger' : 'text-success' }}">
                            ₨{{ number_format(abs($customer->opening_balance), 2) }}
                            @if($customer->opening_balance > 0)
                                <small class="text-muted">(Dr)</small>
                            @elseif($customer->opening_balance < 0)
                                <small class="text-muted">(Cr)</small>
                            @endif
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-icon btn-sm btn-outline-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-icon btn-sm btn-outline-warning" title="Edit Customer">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" onclick="return confirm('Delete this customer? This action cannot be undone.')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">No customers found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div class="card-footer border-top py-3">
        <div class="d-flex justify-content-end">
            {!! $customers->links() !!}
        </div>
    </div>
    @endif
</div>

@endsection
