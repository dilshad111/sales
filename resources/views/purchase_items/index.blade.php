@extends('layouts.app')

@section('title', 'Purchase Items')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 mt-2">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-shopping-basket mr-2 text-primary"></i>Purchase Items
    </h1>
    <a href="{{ route('purchase_items.create') }}" class="btn btn-primary shadow-sm fw-bold">
        <i class="fas fa-plus mr-1"></i> Add Purchase Item
    </a>
</div>

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase small font-weight-bold">
                    <tr>
                        <th class="ps-4 py-3">Item Name</th>
                        <th class="py-3">Supplier</th>
                        <th class="py-3">Unit</th>
                        <th class="py-3 text-end">Purchase Price</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="text-center py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="font-weight-bold text-dark">{{ $item->name }}</div>
                        </td>
                        <td>{{ $item->supplier->name ?? 'Global' }}</td>
                        <td>{{ $item->unit ?? '-' }}</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($item->purchase_price, 2) }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-{{ $item->status == 'active' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $item->status == 'active' ? 'success' : 'danger' }} border border-{{ $item->status == 'active' ? 'success' : 'danger' }} px-3">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('purchase_items.edit', $item) }}" class="btn btn-outline-warning border-0" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('purchase_items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger border-0" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted mb-3"><i class="fas fa-box-open fa-3x opacity-25"></i></div>
                            <h5 class="text-muted">No purchase items found.</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection
