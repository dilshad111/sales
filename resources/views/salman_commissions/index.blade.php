@extends('layouts.app')

@section('title', 'Salman Commissions')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Commissions</li>
                    </ol>
                </nav>
                <h3 class="h4 mb-0 fw-bold text-gray-800 d-flex align-items-center">
                    <span class="bg-info bg-opacity-10 p-2 rounded-3 me-2">
                        <i class="fas fa-percent text-info fa-sm"></i>
                    </span>
                    Salman Commissions
                </h3>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('salman_commissions.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
                    <i class="fas fa-plus me-1"></i>Generate Commission
                </a>
            </div>
        </div>
    </div>
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
                        <td class="text-end fw-bold text-success">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($commission->amount, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary rounded-pill">{{ $commission->details->count() }} Bills</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('salman_commissions.show', $commission) }}" class="btn btn-outline-info" title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('salman_commissions.destroy', $commission) }}" method="POST" class="d-inline">
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No commissions recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($commissions->count() > 0)
                <tfoot class="bg-light fw-bold border-top-2">
                    <tr>
                        <td colspan="3" class="text-end ps-3">PAGE TOTAL</td>
                        <td class="text-end text-success" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                            {{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($commissions->sum('amount'), 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
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
