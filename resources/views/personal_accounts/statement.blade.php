@extends('layouts.app')

@section('title', 'Account Statement')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1><i class="fas fa-file-alt me-2"></i>Account Statement - {{ $user->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('personal_accounts.statement.pdf', $user) . '?' . http_build_query($filters) }}" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf me-1"></i>Export PDF
        </a>
        <a href="{{ route('personal_accounts.statement.csv', $user) . '?' . http_build_query($filters) }}" class="btn btn-outline-success">
            <i class="fas fa-file-csv me-1"></i>Export CSV
        </a>
        <a href="{{ route('personal_accounts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

@if($filters['from_date'] || $filters['to_date'])
<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>
    Showing statement for period: <strong>{{ $filters['from_date'] ? \Carbon\Carbon::parse($filters['from_date'])->format('d/m/Y') : 'Beginning' }}</strong> to <strong>{{ $filters['to_date'] ? \Carbon\Carbon::parse($filters['to_date'])->format('d/m/Y') : 'Present' }}</strong>
</div>
@endif

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">Total Commission</h5>
                <h3 class="text-primary">₨{{ number_format($statement['commission_total'], 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title text-success">Total Payments</h5>
                <h3 class="text-success">₨{{ number_format($statement['payment_total'], 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title {{ $statement['balance'] > 0 ? 'text-danger' : 'text-success' }}">Current Balance</h5>
                <h3 class="{{ $statement['balance'] > 0 ? 'text-danger' : 'text-success' }}">₨{{ number_format($statement['balance'], 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Transaction History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="text-end">Amount (₨)</th>
                        <th class="text-end">Balance (₨)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statement['entries'] as $entry)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}</td>
                        <td>
                            @if($entry['type'] === 'commission')
                                <span class="badge bg-primary">Commission</span>
                            @elseif($entry['type'] === 'payment')
                                <span class="badge bg-success">Payment</span>
                            @endif
                        </td>
                        <td>
                            @if($entry['type'] === 'commission')
                                Commission {{ $entry['reference'] ? "({$entry['reference']})" : '#' . $entry['id'] }}
                                @if($entry['notes'])
                                    <br><small class="text-muted">{{ $entry['notes'] }}</small>
                                @endif
                            @elseif($entry['type'] === 'payment')
                                Payment {{ $entry['reference'] ? "({$entry['reference']})" : '#' . $entry['id'] }}
                                @if($entry['commission_ref'])
                                    <br><small class="text-muted">Applied to: {{ $entry['commission_ref'] }}</small>
                                @endif
                                @if($entry['notes'])
                                    <br><small class="text-muted">{{ $entry['notes'] }}</small>
                                @endif
                            @endif
                        </td>
                        <td class="text-end">
                            @if($entry['type'] === 'commission')
                                <span class="text-danger">+{{ number_format($entry['amount'], 2) }}</span>
                            @elseif($entry['type'] === 'payment')
                                <span class="text-success">({{ number_format($entry['amount'], 2) }})</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold {{ $entry['running_balance'] > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($entry['running_balance'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No transactions found for the selected period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
