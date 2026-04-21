@extends('layouts.app')

@section('title', 'Recovery Entry')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <form action="{{ route('recoveries.store') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-warning text-dark">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-hand-holding-dollar me-2"></i>New Recovery Entry</h5>
                    <div class="fw-bold">No: {{ $recoveryNumber }}</div>
                    <input type="hidden" name="recovery_number" value="{{ $recoveryNumber }}">
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Recovery Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Select Purchase Invoice <span class="text-danger">*</span></label>
                            <select name="purchase_invoice_id" id="purchase_invoice_id" class="form-select select2" required>
                                <option value="">Select Invoice</option>
                                @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id }}" 
                                            data-agent="{{ $invoice->agent->name ?? 'Direct' }}" 
                                            data-comm="{{ $invoice->commission_percentage }}"
                                            data-pending="{{ $invoice->gross_amount - $invoice->recoveries()->sum('amount') }}">
                                        {{ $invoice->invoice_number }} - {{ $invoice->supplier->name }} (Pending: {{ number_format($invoice->gross_amount - $invoice->recoveries()->sum('amount'), 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="invoice_info" class="p-3 bg-light rounded mb-4 d-none border">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="small text-muted mb-1">Assigned Agent</div>
                                <div class="fw-bold text-primary" id="info_agent">-</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted mb-1">Commission Rate</div>
                                <div class="fw-bold text-info" id="info_comm">0.00%</div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="small text-muted mb-1">Outstanding Balance</div>
                                <div class="fw-bold fs-5 text-danger" id="info_pending">0.00</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Recovery Amount (Gross) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-label-warning fw-bold">Rs.</span>
                            <input type="number" step="0.01" name="amount" id="recovery_amount" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">Commission Deduction</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">-</span>
                                <input type="text" id="comm_deduct_display" class="form-control" value="0.00" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success">Net Profit to Transfer</label>
                            <div class="input-group">
                                <span class="input-group-text bg-label-success text-white">=</span>
                                <input type="text" id="net_transfer_display" class="form-control fw-bold" value="0.00" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 pt-3 border-top">
                        <label class="form-label fw-bold">Transfer To (Payment Party / Partner) <span class="text-danger">*</span></label>
                        <select name="director_account_id" class="form-select" required>
                            <option value="">Select Account</option>
                            @foreach($directorAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">The Net Profit after commission will be credited to this account.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Internal Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Reference, payment mode details, etc..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="{{ route('recoveries.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning btn-lg px-5">
                            <i class="fas fa-check-circle me-1"></i> Confirm Recovery
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let activeComm = 0;

    $('#purchase_invoice_id').on('change', function() {
        let opt = $(this).find('option:selected');
        if (opt.val()) {
            activeComm = parseFloat(opt.data('comm')) || 0;
            $('#info_agent').text(opt.data('agent'));
            $('#info_comm').text(activeComm.toFixed(2) + '%');
            $('#info_pending').text(parseFloat(opt.data('pending')).toLocaleString(undefined, {minimumFractionDigits: 2}));
            $('#invoice_info').removeClass('d-none');
            
            // Re-calc if amount exists
            calculateFlow();
        } else {
            $('#invoice_info').addClass('d-none');
        }
    });

    $('#recovery_amount').on('input', calculateFlow);

    function calculateFlow() {
        let totalVal = parseFloat($('#recovery_amount').val()) || 0;
        let commAmt = (totalVal * activeComm) / 100;
        let netAmt = totalVal - commAmt;

        $('#comm_deduct_display').val(commAmt.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#net_transfer_display').val(netAmt.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }
});
</script>
@endpush
@endsection
