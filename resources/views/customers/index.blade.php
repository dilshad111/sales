@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users me-2 text-primary"></i>Customer Directory
        </h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> Add New Customer
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('customers.index') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group input-group-merge shadow-none">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Search by name, email, or phone..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 Records</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Records</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Records</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            @if(request()->has('search'))
                                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers List Table -->
    <div class="card shadow border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 25%;">Customer Details</th>
                        <th style="width: 15%;">Contact</th>
                        <th style="width: 10%;">Type</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th class="text-center" style="width: 12%;">Excess Qty (%)</th>
                        <th class="text-end" style="width: 15%;">Opening Balance</th>
                        <th class="text-center" style="width: 13%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($customers as $customer)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark fs-6">{{ $customer->name }}</span>
                                <small class="text-muted text-truncate" style="max-width: 250px;">{{ $customer->address }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span><i class="fas fa-phone-alt me-1 text-muted small"></i> {{ $customer->phone }}</span>
                                <small class="text-muted"><i class="fas fa-envelope me-1 small"></i> {{ $customer->email }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary text-capitalize">{{ $customer->type }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-{{ $customer->status == 'active' ? 'success' : 'danger' }} rounded-pill">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-primary">{{ (float)$customer->excess_qty_percent }}%</span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold {{ $customer->opening_balance >= 0 ? 'text-danger' : 'text-success' }}">
                                ₨ {{ number_format(abs($customer->opening_balance), 2) }}
                                @if($customer->opening_balance > 0)
                                    <small class="text-muted">(Dr)</small>
                                @elseif($customer->opening_balance < 0)
                                    <small class="text-muted">(Cr)</small>
                                @endif
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-icon btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-icon btn-sm btn-outline-warning" title="Edit Customer">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                                <span class="text-muted fs-5">No customers matching your criteria.</span>
                                <a href="{{ route('customers.index') }}" class="btn btn-link">Clear Filters</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries</small>
                {!! $customers->links() !!}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-danger { background-color: #ffe0db !important; color: #ff3e1d !important; }
    .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
    .badge.rounded-pill { padding: 0.45em 0.85em; }
    .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.375rem; }
    .table thead th { border-top: none; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
</style>
@endsection
