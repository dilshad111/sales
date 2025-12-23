@extends('layouts.app')

@section('title', 'Edit Payment #' . $payment->id)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2 text-warning"></i>Edit Payment</h1>
    <a href="{{ route('payments.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm me-1"></i>Back to List
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Payment Information - {{ $payment->customer->name }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.update', $payment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Bill #</label>
                            <input type="text" class="form-control bg-light" value="{{ $payment->bill->bill_number }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_date" class="form-label fw-bold">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mode" class="form-label fw-bold">Payment Mode</label>
                            <select name="mode" id="mode" class="form-select @error('mode') is-invalid @enderror" required>
                                <option value="cash" {{ old('mode', $payment->mode) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank" {{ old('mode', $payment->mode) == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="upi" {{ old('mode', $payment->mode) == 'upi' ? 'selected' : '' }}>UPI</option>
                                <option value="other" {{ old('mode', $payment->mode) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('mode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="payment_party_id" class="form-label fw-bold">Payment Party</label>
                            <select name="payment_party_id" id="payment_party_id" class="form-select @error('payment_party_id') is-invalid @enderror">
                                <option value="">Select Party</option>
                                @foreach($paymentParties as $party)
                                    <option value="{{ $party->id }}" {{ old('payment_party_id', $payment->payment_party_id) == $party->id ? 'selected' : '' }}>
                                        {{ $party->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_party_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label fw-bold">Payment Amount (Rs.)</label>
                        <div class="input-group">
                            <span class="input-group-text">₨</span>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $payment->amount) }}" required>
                        </div>
                        <div class="form-text">Max available: {{ number_format($payment->bill->total - $payment->bill->payments()->where('id', '!=', $payment->id)->sum('amount'), 2) }}</div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3">{{ old('remarks', $payment->remarks) }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">
                            <i class="fas fa-save me-1"></i>Update Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
