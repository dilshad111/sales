@extends('layouts.app')

@section('title', 'Carton Costing Report')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
            <div>
                <h1 class="display-6 mb-2 mb-md-0"><i class="fas fa-list me-2"></i>Carton Costing Records</h1>
                <p class="text-muted mb-0">Review saved costings, filter by customer, and manage records.</p>
            </div>
            <a href="{{ route('carton_costing.index') }}" class="btn btn-primary mt-3 mt-md-0"><i class="fas fa-plus me-2"></i>New Carton Costing</a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('carton_costing.report') }}">
            <div class="col-md-4">
                <label for="filter_customer" class="form-label">Customer</label>
                <select class="form-select" id="filter_customer" name="customer_id">
                    <option value="">All Customers</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ (int) ($filters['customer_id'] ?? 0) === (int) $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_search" class="form-label">Search</label>
                <input type="text" class="form-control" id="filter_search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by ID, FEFCO Code, or cost">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search me-2"></i>Apply Filters</button>
                <a href="{{ route('carton_costing.report') }}" class="btn btn-outline-secondary"><i class="fas fa-undo me-2"></i>Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($costings->count())
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>FEFCO Code</th>
                            <th>Ply</th>
                            <th>Dimensions (L × W × H mm)</th>
                            <th class="text-end">Final Cost (₨)</th>
                            <th class="text-end">Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($costings as $costing)
                            <tr>
                                <td>{{ $costing->id }}</td>
                                <td>{{ $costing->customer?->name ?? '—' }}</td>
                                <td>{{ $costing->fefco_code }}</td>
                                <td>{{ $costing->ply }}</td>
                                <td>{{ number_format($costing->length, 2) }} × {{ number_format($costing->width, 2) }} × {{ number_format($costing->height, 2) }}</td>
                                <td class="text-end">{{ number_format($costing->final_carton_cost, 2) }}</td>
                                <td class="text-end">{{ $costing->created_at->format('d M Y, h:i A') }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('carton_costing.edit', $costing) }}" class="btn btn-outline-primary"><i class="fas fa-pen"></i></a>
                                        <a href="{{ route('carton_costing.print', $costing) }}" class="btn btn-outline-secondary" target="_blank"><i class="fas fa-print"></i></a>
                                        <form action="{{ route('carton_costing.destroy', $costing) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this costing?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $costings->links() }}
            </div>
        @else
            <div class="p-5 text-center">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No carton costings found.</h4>
                <p class="mb-0">Try adjusting your filters or <a href="{{ route('carton_costing.index') }}">create a new costing</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection
