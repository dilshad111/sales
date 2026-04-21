@extends('layouts.app')

@section('title', 'Items')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Finance /</span> Items
    </h4>
    <a href="{{ route('items.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="fas fa-plus me-1"></i> Add Item
    </a>
</div>

<!-- Search & Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-2">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="fas fa-list text-muted"></i></span>
                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 Records</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Records</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Records</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search item name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="customer_id" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Items List Card -->
<div class="card shadow-none border">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Code</th>
                    <th>Customer</th>
                    <th>Item Name</th>
                    <th>UoM</th>
                    <th>Price</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($items as $item)
                <tr>
                    <td class="ps-4"><code>{{ $item->code }}</code></td>
                    <td><span class="fw-semibold">{{ $item->customer->name }}</span></td>
                    <td>{{ $item->name }}</td>
                    <td><span class="badge bg-label-info">{{ $item->uom }}</span></td>
                    <td class="fw-bold text-dark">₨{{ number_format($item->price, 2) }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('items.show', $item) }}" class="btn btn-icon btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-icon btn-sm btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('items.destroy', $item) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">No items found matching your criteria.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($items->hasPages())
    <div class="card-footer border-top py-3">
        <div class="d-flex justify-content-end">
            {!! $items->links() !!}
        </div>
    </div>
    @endif
</div>

@endsection
