@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 mt-2">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-truck-loading mr-2 text-primary"></i>Suppliers
    </h1>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary shadow-sm fw-bold">
        <i class="fas fa-plus mr-1"></i> Add Supplier
    </a>
</div>

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase small font-weight-bold">
                    <tr>
                        <th class="ps-4 py-3">Supplier Name</th>
                        <th class="py-3">Phone</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Status</th>
                        <th class="text-center py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td class="ps-4">
                            <div class="font-weight-bold text-dark">{{ $supplier->name }}</div>
                            <small class="text-muted">{{ Str::limit($supplier->address, 50) }}</small>
                        </td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $supplier->status == 'active' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $supplier->status == 'active' ? 'success' : 'danger' }} border border-{{ $supplier->status == 'active' ? 'success' : 'danger' }} px-3">
                                {{ ucfirst($supplier->status) }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-warning border-0" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
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
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted mb-3"><i class="fas fa-user-slash fa-3x opacity-25"></i></div>
                            <h5 class="text-muted">No suppliers found.</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>
@endsection
