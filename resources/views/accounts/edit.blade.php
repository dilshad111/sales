@extends('layouts.app')

@section('title', 'Edit Account')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-primary font-weight-bold">
                    <i class="fas fa-edit me-2 text-info"></i>Edit Account
                </h5>
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-none">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('accounts.update', $account) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-muted small font-weight-bold">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light @error('name') is-invalid @enderror" value="{{ old('name', $account->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold">Type <span class="text-danger">*</span></label>
                            @if($account->type === 'customer')
                                <input type="text" class="form-control bg-light opacity-75" value="Customer" readonly>
                                <input type="hidden" name="type" value="customer">
                            @else
                                <select name="type" class="form-select bg-light @error('type') is-invalid @enderror" required>
                                    <option value="supplier" {{ old('type', $account->type) == 'supplier' ? 'selected' : '' }}>Supplier</option>
                                    <option value="general" {{ old('type', $account->type) == 'general' ? 'selected' : '' }}>General Party</option>
                                </select>
                            @endif
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select bg-light @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $account->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $account->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small font-weight-bold">Main Party / Agent (Linked Account)</label>
                            <select name="payment_party_id" class="form-select bg-light select2 @error('payment_party_id') is-invalid @enderror">
                                <option value="">None (Individual Party)</option>
                                @foreach($paymentParties as $party)
                                    <option value="{{ $party->id }}" {{ old('payment_party_id', $account->payment_party_id) == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                                @endforeach
                            </select>
                            @error('payment_party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text small mt-1 text-info"><i class="fas fa-link me-1"></i>Transactions will mirror in this Party's ledger.</div>
                        </div>

                        @if($account->type !== 'customer')
                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold">Opening Balance <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                                <input type="number" step="0.01" name="opening_balance" class="form-control bg-light border-start-0 @error('opening_balance') is-invalid @enderror" value="{{ old('opening_balance', $account->opening_balance) }}" required>
                            </div>
                            @error('opening_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @else
                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold">Opening Balance</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 opacity-75">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                                <input type="text" class="form-control bg-light border-start-0 opacity-75" value="{{ number_format($account->opening_balance, 2) }}" readonly>
                            </div>
                            <div class="form-text small mt-1 font-italic text-muted">Customer opening balance is synced from Customer profile.</div>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control bg-light" value="{{ old('phone', $account->phone) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold">Email Address</label>
                            <input type="email" name="email" class="form-control bg-light" value="{{ old('email', $account->email) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-muted small font-weight-bold">Address</label>
                            <textarea name="address" class="form-control bg-light" rows="2">{{ old('address', $account->address) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-none">
                            <i class="fas fa-save me-1"></i>Update Account
                        </button>
                        <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary px-4 rounded-pill shadow-none">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
