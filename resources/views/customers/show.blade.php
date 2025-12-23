@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<h1><i class="fas fa-user me-2"></i>Customer Details</h1>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $customer->name }}</h5>
        <p><i class="fas fa-phone me-1"></i><strong>Phone:</strong> {{ $customer->phone }}</p>
        <p><i class="fas fa-envelope me-1"></i><strong>Email:</strong> {{ $customer->email }}</p>
        <p><i class="fas fa-map-marker-alt me-1"></i><strong>Address:</strong> {{ $customer->address }}</p>
        <p><i class="fas fa-toggle-on me-1"></i><strong>Status:</strong> <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($customer->status) }}</span></p>
        <p><i class="fas fa-balance-scale me-1"></i><strong>Opening Balance:</strong>
            <span class="{{ $customer->opening_balance >= 0 ? 'text-danger' : 'text-success' }}">
                ₨{{ number_format(abs($customer->opening_balance), 2) }}
                @if($customer->opening_balance > 0)
                    <small class="text-muted">(Debit - Customer owes)</small>
                @elseif($customer->opening_balance < 0)
                    <small class="text-muted">(Credit - Customer is owed)</small>
                @else
                    <small class="text-muted">(Balanced)</small>
                @endif
            </span>
        </p>
    </div>
</div>
<a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning mt-3"><i class="fas fa-edit me-1"></i>Edit</a>
<a href="{{ route('customers.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left me-1"></i>Back</a>
@endsection
