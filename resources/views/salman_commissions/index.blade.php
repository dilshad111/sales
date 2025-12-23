@extends('layouts.app')

@section('title', 'Salman Commissions')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-percent me-2 text-info"></i>Salman Commissions</h1>
    <a href="{{ route('salman_commissions.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 me-1"></i>Generate Commission
    </a>
</div>

<div class="card shadow-sm border-0 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>User (Commissionee)</th>
                        <th>Customer</th>
                        <th class="text-end">Total Commission</th>
                        <th class="text-center">Bills Count</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commissions as $commission)
                    <tr>
                        <td class="ps-3">{{ $commission->commission_date->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ $commission->user->name }}</td>
                        <td>{{ $commission->customer->name ?? '-' }}</td>
                        <td class="text-end fw-bold text-success">Rs. {{ number_format($commission->amount, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary rounded-pill">{{ $commission->details->count() }} Bills</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('salman_commissions.show', $commission) }}" class="btn btn-outline-info" title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('salman_commissions.destroy', $commission) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this commission record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No commissions recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($commissions->hasPages())
    <div class="card-footer bg-white">
        {{ $commissions->links() }}
    </div>
    @endif
</div>
@endsection
