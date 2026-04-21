@extends('layouts.app')

@section('title', 'Payment #' . $payment->id)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h2 mb-0 text-gray-800"><i class="fas fa-receipt me-2 text-primary"></i>Payment Details</h1>
    <div class="btn-group shadow-sm">
        <a href="{{ route('payments.edit', $payment) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </form>
        <a href="{{ route('payments.print', $payment) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-print me-1"></i>Print Voucher
        </a>
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
                    <div class="col-sm-4">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Customer</label>
                        <div class="h5 mb-0 font-weight-bold">{{ $payment->customer->name }}</div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Date & Mode</label>
                        <div class="h6 mb-0 font-weight-bold">
                            <i class="far fa-calendar-alt me-1 text-primary"></i>{{ $payment->payment_date->format('d M, Y') }}
                            <span class="badge bg-{{ $payment->mode == 'cash' ? 'success' : 'info' }} ms-2">
                                {{ strtoupper($payment->mode) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Payment Party / Recipient</label>
                        <div class="h6 mb-0 font-weight-bold">
                            @if($payment->destination_type !== 'cash_bank')
                                <span class="badge bg-label-warning small mb-1">
                                    {{ $payment->destination_type == 'director' ? 'Managing Partner' : 'Associated Partner' }}
                                </span>
                                <br>
                                {{ optional(\App\Models\Account::find($payment->recipient_account_id))->name ?? '-' }}
                            @else
                                {{ optional($payment->paymentParty)->name ?? '-' }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-12">
                        <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Bill Settlements Breakup</label>
                        <div class="table-responsive border rounded mb-3">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bill #</th>
                                        <th>Bill Date</th>
                                        <th class="text-end">Amount Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->settlements as $settlement)
                                    <tr>
                                        <td>
                                            @if($settlement->bill)
                                                <a href="{{ route('bills.show', $settlement->bill_id) }}" class="text-decoration-none fw-bold">
                                                    {{ $settlement->bill->bill_number }}
                                                </a>
                                            @else
                                                <span class="badge bg-info text-white small">O/B</span>
                                            @endif
                                        </td>
                                        <td class="small">{{ optional($settlement->bill)->bill_date?->format('d/m/Y') }}</td>
                                        <td class="text-end fw-bold">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($settlement->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="2" class="text-end">Total Allocation:</td>
                                        <td class="text-end text-success">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <label class="text-xs font-weight-bold text-uppercase mb-1 d-block text-muted">Remarks</label>
                    <p class="mb-0 text-gray-800 font-italic small">{{ $payment->remarks ?: 'No remarks provided.' }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-left-success h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center py-5">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Amount Paid</div>
                <div class="display-6 font-weight-bold text-gray-800">{{ $companySetting->currency_symbol ?? 'Rs.' }} {{ number_format($payment->amount, 2) }}</div>
                <div class="mt-3">
                    <i class="fas fa-check-circle fa-3x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
