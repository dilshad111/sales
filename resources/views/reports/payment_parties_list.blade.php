@extends('layouts.app')

@section('title', 'Payment Parties List')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Reports /</span> Payment Parties List
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('payment_parties.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-cog me-1"></i> Manage Parties
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Party Name</th>
                    <th>Contact Info</th>
                    <th class="text-end">Opening Balance</th>
                    <th class="text-end">Current Balance</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parties as $party)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-primary">{{ $party->name }}</div>
                        @if($party->account)
                            <span class="badge bg-label-info" style="font-size: 0.7rem;">Ledger Linked</span>
                        @endif
                    </td>
                    <td>
                        <div class="small"><i class="fas fa-phone me-1 text-muted"></i>{{ $party->phone ?? '-' }}</div>
                        <div class="small"><i class="fas fa-envelope me-1 text-muted"></i>{{ $party->email ?? '-' }}</div>
                    </td>
                    <td class="text-end">
                        {{ number_format($party->opening_balance, 2) }}
                    </td>
                    <td class="text-end fw-bold {{ ($party->account?->balance ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($party->account?->balance ?? 0, 2) }}
                    </td>
                    <td class="text-center">
                        @if($party->account)
                        <a href="{{ route('reports.payment_party_statement', $party) }}" class="btn btn-sm btn-label-primary">
                            <i class="fas fa-file-invoice me-1"></i> Statement
                        </a>
                        @else
                            <span class="text-muted small">No Ledger</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No payment parties found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
