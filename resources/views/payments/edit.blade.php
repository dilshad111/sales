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
                            <label for="payment_date" class="form-label fw-bold">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                    </div>

                    <div class="mb-4">
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

                    <div class="mb-4">
                        <label class="form-label fw-bold h6 mb-3">Allocated Settlements</label>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 py-2">Bill #</th>
                                        <th class="text-end">Outstanding</th>
                                        <th style="width: 180px;" class="text-end pe-3">Paid Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->settlements as $settlement)
                                    @php
                                        $bill = $settlement->bill;
                                        if ($bill) {
                                            $otherPaymentsSum = $bill->payments()->where('payment_id', '!=', $payment->id)->sum('amount');
                                            $maxAvailable = $bill->total - $otherPaymentsSum;
                                            $title = $bill->bill_number;
                                            $subtitle = $bill->bill_date->format('d/m/Y');
                                        } else {
                                            $otherPaymentsSum = \App\Models\PaymentSettlement::whereHas('payment', function($q) use ($payment) {
                                                $q->where('customer_id', $payment->customer_id);
                                            })->whereNull('bill_id')->where('payment_id', '!=', $payment->id)->sum('amount');
                                            $maxAvailable = $payment->customer->opening_balance - $otherPaymentsSum;
                                            $title = 'O/B';
                                            $subtitle = 'Opening Balance';
                                        }
                                    @endphp
                                    <tr class="align-middle">
                                        <td class="ps-3 py-2">
                                            <div class="fw-bold">{{ $title }}</div>
                                            <div class="small text-muted">{{ $subtitle }}</div>
                                        </td>
                                        <td class="text-end text-muted font-monospace small">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($maxAvailable, 2) }}</td>
                                        <td class="pe-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text badge bg-light text-dark border-end-0">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                                                <input type="number" step="0.01" max="{{ $maxAvailable }}" name="settlements[{{ $settlement->id }}]" class="form-control text-end font-weight-bold border-start-0" value="{{ old('settlements.' . $settlement->id, $settlement->amount) }}" required>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="form-text small mt-2">Adjust individual bill settlements if needed. The total payment amount will be automatically recalculated.</div>
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
