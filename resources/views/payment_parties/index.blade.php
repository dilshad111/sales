@extends('layouts.app')

@section('title', 'Payment Parties')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Finance /</span> Payment Parties
    </h4>
    <a href="{{ route('payment_parties.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="fas fa-plus me-1"></i> Add Payment Party
    </a>
</div>

<div class="card shadow-none border">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Party Name</th>
                    <th>Contact Info</th>
                    <th>Address</th>
                    <th class="text-end">Opening Balance</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($parties as $party)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-primary">{{ $party->name }}</div>
                        <small class="text-muted">ID: #{{ $party->id }}</small>
                    </td>
                    <td>
                        <div class="small"><i class="fas fa-phone me-1 text-muted"></i>{{ $party->phone ?? '-' }}</div>
                        <div class="small"><i class="fas fa-envelope me-1 text-muted"></i>{{ $party->email ?? '-' }}</div>
                    </td>
                    <td>
                        <span class="small text-wrap" style="max-width: 150px; display: block;">{{ Str::limit($party->address ?? '-', 40) }}</span>
                    </td>
                    <td class="text-end fw-bold">
                        {{ number_format($party->opening_balance, 2) }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-label-{{ $party->status == 'active' ? 'success' : 'danger' }} rounded-pill text-uppercase" style="font-size: 10px;">
                            {{ $party->status }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('payment_parties.edit', $party) }}" class="btn btn-icon btn-sm btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('payment_parties.destroy', $party) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">No payment parties found.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
