@extends('layouts.app')

@section('title', 'Payment #' . $payment->id)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h2 mb-0 text-gray-800"><i class="fas fa-receipt me-2 text-primary"></i>Payment Details</h1>
    <div class="btn-group shadow-sm">
        <a href="{{ route('payments.edit', $payment) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </form>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Customer</label>
                        <div class="h5 mb-0 font-weight-bold">{{ $payment->customer->name }}</div>
                        <div class="text-muted small">{{ $payment->customer->address }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Payment Party</label>
                        <div class="h5 mb-0 font-weight-bold">{{ optional($payment->paymentParty)->name ?? '-' }}</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Payment Date</label>
                        <div class="h6 font-weight-bold"><i class="far fa-calendar-alt me-1"></i>{{ $payment->payment_date->format('d M, Y') }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Payment Mode</label>
                        <div class="h6 font-weight-bold">
                            <span class="badge bg-{{ $payment->mode == 'cash' ? 'success' : 'info' }}">
                                {{ strtoupper($payment->mode) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Bill Association</label>
                        <div class="h6 font-weight-bold">
                            <a href="{{ route('bills.show', $payment->bill_id) }}" class="text-decoration-none">
                                <i class="fas fa-file-invoice me-1"></i>{{ $payment->bill->bill_number }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Remarks</label>
                    <p class="mb-0 text-gray-800">{{ $payment->remarks ?: 'No remarks provided.' }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-left-success h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center py-5">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Amount Paid</div>
                <div class="display-6 font-weight-bold text-gray-800">Rs. {{ number_format($payment->amount, 2) }}</div>
                <div class="mt-3">
                    <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
