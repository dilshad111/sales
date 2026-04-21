@extends('layouts.app')

@section('title', 'Recoveries Tracking')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Purchases /</span> Recoveries Tracking
    </h4>
    <a href="{{ route('recoveries.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="fas fa-hand-holding-dollar me-1"></i> New Recovery Entry
    </a>
</div>

<div class="card shadow-none border">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Rec # & Date</th>
                    <th>Purchase Inv</th>
                    <th>Agent</th>
                    <th class="text-end">Recovery Amt</th>
                    <th class="text-end">Comm Deduct</th>
                    <th class="text-end">Net Transfer</th>
                    <th>Transfer To</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($recoveries as $recovery)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $recovery->recovery_number }}</div>
                        <div class="small text-muted">{{ $recovery->date->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $recovery->invoice->invoice_number }}</div>
                        <div class="small text-muted">{{ $recovery->invoice->supplier->name }}</div>
                    </td>
                    <td>{{ $recovery->agent->name ?? 'Direct' }}</td>
                    <td class="text-end fw-bold text-warning">
                        {{ number_format($recovery->amount, 2) }}
                    </td>
                    <td class="text-end text-danger">
                        -{{ number_format($recovery->commission_deducted, 2) }}
                    </td>
                    <td class="text-end fw-bold text-success">
                        {{ number_format($recovery->net_amount_transfered, 2) }}
                    </td>
                    <td>
                        <span class="badge bg-label-primary text-uppercase" style="font-size: 10px;">
                            {{ $recovery->directorAccount->name }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <form method="POST" action="{{ route('recoveries.destroy', $recovery) }}" class="d-inline">
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
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted">No recovery entries found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($recoveries->hasPages())
    <div class="card-footer border-top py-3">
        <div class="d-flex justify-content-end">
            {!! $recoveries->links() !!}
        </div>
    </div>
    @endif
</div>
@endsection
