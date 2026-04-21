@extends('layouts.app')

@section('title', 'Party Ledger')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 animate__animated animate__fadeIn">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary font-weight-bold text-center">
                    <i class="fas fa-book me-2"></i>Party Ledger Statement
                </h5>
            </div>
            <div class="card-body">
                 <form action="{{ route('ledger.index') }}" method="GET" id="ledger_form">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-muted small font-weight-bold">Select Account / Party <span class="text-danger">*</span></label>
                            <select name="account" id="account_select" class="form-select select2 bg-light" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->name }} 
                                        ({!! $account->type == 'director' ? 'Principal' : ($account->type == 'friend' ? 'Special Partner' : ucfirst($account->type)) !!})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control bg-light" value="{{ date('Y-m-01') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small font-weight-bold">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control bg-light" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill shadow-lg py-2">
                            <i class="fas fa-eye me-1"></i>View Statement
                        </button>
                        <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm py-2">
                            <i class="fas fa-list me-1"></i>View All Accounts
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#ledger_form')
        });

        // Dynamic URL update on submit
        $('#ledger_form').on('submit', function(e) {
            e.preventDefault();
            const accountId = $('#account_select').val();
            if (!accountId) {
                alert('Please select an account first.');
                return;
            }
            
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            
            // Build the clean URL: ledger/{id}?start_date=...&end_date=...
            let url = "{{ route('ledger.show', ['account' => ':id']) }}";
            url = url.replace(':id', accountId);
            
            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            
            window.location.href = url + '?' + params.toString();
        });
    });
</script>
@endpush
@endsection
