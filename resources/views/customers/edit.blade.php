@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<h1><i class="fas fa-user-edit me-2"></i>Edit Customer</h1>
<form method="POST" action="{{ route('customers.update', $customer) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="name" class="form-label"><i class="fas fa-user me-1"></i>Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $customer->name }}" required>
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label"><i class="fas fa-phone me-1"></i>Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ $customer->phone }}" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Email</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ $customer->email }}" required>
    </div>
    <div class="mb-3">
        <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Address</label>
        <textarea class="form-control" id="address" name="address" rows="3" required>{{ $customer->address }}</textarea>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label"><i class="fas fa-toggle-on me-1"></i>Status</label>
        <select class="form-control" id="status" name="status" required>
            <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="type" class="form-label"><i class="fas fa-tag me-1"></i>Customer Type</label>
        <select class="form-control" id="type" name="type" required>
            <option value="Official" {{ $customer->type == 'Official' ? 'selected' : '' }}>Official</option>
            <option value="Un-Official" {{ $customer->type == 'Un-Official' ? 'selected' : '' }}>Un-Official</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="opening_balance" class="form-label"><i class="fas fa-balance-scale me-1"></i>Opening Balance (₨)</label>
        <input type="number" step="0.01" class="form-control" id="opening_balance" name="opening_balance" value="{{ $customer->opening_balance }}" placeholder="0.00">
        <small class="form-text text-muted">Enter positive for debit (customer owes) or negative for credit (customer is owed).</small>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
</form>
@endsection
