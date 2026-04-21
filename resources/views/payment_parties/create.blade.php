@extends('layouts.app')

@section('title', 'Add Payment Party')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary py-3">
                <h5 class="mb-0 fw-bold text-white"><i class="fas fa-plus me-2"></i>Add New Payment Party</h5>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('payment_parties.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Full Name / Party Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Nadir Arshad">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="03xx-xxxxxxx">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@example.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Full business or residential address"></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Opening Balance <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" name="opening_balance" class="form-control @error('opening_balance') is-invalid @enderror" value="{{ old('opening_balance', 0) }}" required>
                            </div>
                            @error('opening_balance') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Account Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-check-circle me-1"></i> Save Payment Party
                        </button>
                        <a href="{{ route('payment_parties.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="alert alert-outline-info mt-4 d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <div>
                <small>Creating a Payment Party will automatically generate a corresponding ledger account in the system.</small>
            </div>
        </div>
    </div>
</div>
@endsection
