@extends('layouts.app')

@section('title', 'Purchase Delivery Challans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-primary fw-bold"><i class="fas fa-truck-loading me-2"></i>Purchase Delivery Challans</h4>
    <a href="{{ route('purchase_delivery_challans.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-1"></i> Create Purchase DC
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">System DC#</th>
                        <th class="py-3">Supplier DC#</th>
                        <th class="py-3">Date</th>
                        <th class="py-3">Supplier</th>
                        <th class="py-3">Vehicle #</th>
                        <th class="py-3 text-end">Total Amount</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($challans as $challan)
                    <tr>
                        <td class="px-4 fw-bold text-dark">{{ $challan->challan_number }}</td>
                        <td class="text-secondary small">{{ $challan->supplier_dc_number ?: '-' }}</td>
                        <td>{{ $challan->date->format('d-m-Y') }}</td>
                        <td>{{ $challan->supplier->name }}</td>
                        <td>{{ $challan->vehicle_number ?: '-' }}</td>
                        <td class="text-end fw-bold text-primary">Rs. {{ number_format($challan->total_amount, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-label-info">{{ ucfirst($challan->status) }}</span>
                        </td>
                        <td class="px-4 text-center">
                            <div class="btn-group">
                                <a href="{{ route('purchase_delivery_challans.show', $challan) }}" class="btn btn-sm btn-icon border-0" data-bs-toggle="tooltip" title="View Details">
                                    <i class="fas fa-eye text-info"></i>
                                </a>
                                <a href="{{ route('purchase_delivery_challans.edit', $challan) }}" class="btn btn-sm btn-icon border-0" data-bs-toggle="tooltip" title="Edit DC">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                                <form action="{{ route('purchase_delivery_challans.destroy', $challan) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this DC?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon border-0" data-bs-toggle="tooltip" title="Delete DC">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No Purchase Delivery Challans found.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($challans->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $challans->links() }}
    </div>
    @endif
</div>
@endsection
