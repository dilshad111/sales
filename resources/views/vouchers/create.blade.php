@extends('layouts.app')

@section('title', 'Create ' . $type)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-8">

            <div class="card shadow-lg border-0 mb-4 overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-primary text-white p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="h4 mb-0 font-weight-bold">
                                @php
                                     $icon = match($type) {
                                         'PV' => 'fa-money-check-dollar',
                                         'RV' => 'fa-receipt',
                                         'JV' => 'fa-file-signature',
                                         default => 'fa-file-invoice'
                                     };
                                     $title = match($type) {
                                         'PV' => 'Payment Voucher',
                                         'RV' => 'Receive Voucher',
                                         'JV' => 'Journal Voucher',
                                         default => $type
                                     };
                                 @endphp
                                 <i class="fas {{ $icon }} mr-2"></i>New {{ $title }}
                             </h2>
                             <p class="mb-0 small opacity-75">Fill in the details to record a new transaction</p>
                         </div>
                         <a href="{{ route('vouchers.index') }}" class="btn btn-light btn-sm rounded-pill px-3 shadow-none">
                             <i class="fas fa-times me-1"></i> Cancel
                         </a>
                     </div>
                 </div>
                  <div class="card-body p-4 p-lg-5">
                      <form action="{{ route('vouchers.store') }}" method="POST" id="voucherForm">
                          @csrf
                          <input type="hidden" name="type" value="{{ $type }}">

                          @if ($errors->any())
                              <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #ffe5e5; color: #d63384;">
                                  <div class="d-flex p-1">
                                      <i class="fas fa-exclamation-circle mt-1 me-3"></i>
                                      <div>
                                          <div class="fw-bold">Validation Error</div>
                                          <ul class="mb-0 small ps-3">
                                              @foreach ($errors->all() as $error)
                                                  <li>{{ $error }}</li>
                                              @endforeach
                                          </ul>
                                      </div>
                                  </div>
                              </div>
                          @endif

                          @if(session('error'))
                              <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #dc3545; color: #fff;">
                                  <div class="d-flex p-1">
                                      <i class="fas fa-times-circle mt-1 me-3 fs-3"></i>
                                      <div>
                                          <div class="fw-bold fs-5">System Error</div>
                                          <div class="small">{{ session('error') }}</div>
                                      </div>
                                  </div>
                              </div>
                          @endif

                         <div class="row mb-4 g-4">
                             <div class="col-md-3">
                                 <label class="form-label font-weight-bold text-muted small text-uppercase">Transaction Date</label>
                                 <input type="date" name="date" class="form-control form-control-lg border-0 bg-light rounded-3" value="{{ date('Y-m-d') }}" required>
                             </div>
                             @if($type == 'PV' || $type == 'RV')
                             <div class="col-md-3">
                                 <label class="form-label font-weight-bold text-muted small text-uppercase">Mode of Payment</label>
                                 <select name="payment_mode" id="payment_mode" class="form-select form-select-lg border-0 bg-light rounded-3">
                                     <option value="cash">Cash</option>
                                     <option value="bank">Bank</option>
                                 </select>
                             </div>
                             @endif
                             <div class="{{ ($type == 'PV' || $type == 'RV') ? 'col-md-6' : 'col-md-9' }}">
                                 <label class="form-label font-weight-bold text-muted small text-uppercase">Narration / Remarks</label>
                                 <input type="text" name="narration" class="form-control form-control-lg border-0 bg-light rounded-3" placeholder="Enter transaction explanation here..." required>
                             </div>
                         </div>

                         <div id="bank_details_row" class="row mb-4 g-4 d-none animate__animated animate__fadeIn">
                             <div class="col-md-6">
                                 <label class="form-label font-weight-bold text-muted small text-uppercase">Bank</label>
                                 <select name="bank_id" class="form-select form-select-lg border-0 bg-light rounded-3">
                                     <option value="">Select Bank</option>
                                     @foreach($banks as $bank)
                                         <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_number }})</option>
                                     @endforeach
                                 </select>
                             </div>
                             <div class="col-md-6">
                                 <label class="form-label font-weight-bold text-muted small text-uppercase">Cheque Number</label>
                                 <input type="text" name="cheque_number" class="form-control form-control-lg border-0 bg-light rounded-3" placeholder="Enter cheque number">
                             </div>
                         </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-borderless align-middle" id="entriesTable">
                                <thead class="text-muted small text-uppercase font-weight-bold border-bottom">
                                    <tr>
                                        <th style="width: 50%;">Account</th>
                                        <th class="text-end" style="width: 20%;">Debit</th>
                                        <th class="text-end" style="width: 20%;">Credit</th>
                                        <th class="text-center" style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="entriesBody">
                                    <!-- Rows will be added here -->
                                </tbody>
                                <tfoot>
                                    <tr class="border-top">
                                        <td>
                                            <button type="button" class="btn btn-link text-primary font-weight-bold text-decoration-none px-0" onclick="addRow()">
                                                <i class="fas fa-plus-circle me-1"></i> Add Another Line
                                            </button>
                                        </td>
                                        <td class="text-end py-3">
                                            <div class="h5 mb-0 fw-bold" id="totalDebit">0.00</div>
                                            <div class="small text-muted text-uppercase">Total Debit</div>
                                        </td>
                                        <td class="text-end py-3">
                                            <div class="h5 mb-0 fw-bold" id="totalCredit">0.00</div>
                                            <div class="small text-muted text-uppercase">Total Credit</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div id="diffAlert" class="alert alert-soft-danger d-none animate__animated animate__fadeIn">
                            <i class="fas fa-exclamation-triangle me-2"></i> Difference of <span id="diffAmount">0.00</span> detected. Vouchers must be balanced.
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow rounded-pill font-weight-bold" id="submitBtn">
                                <i class="fas fa-save me-2"></i> Save Voucher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Customer Info Sidebar (Dynamic) -->
        <div class="col-xl-3 col-lg-4 d-none" id="customerInfoSidebar">

            <div class="card shadow-sm border-0 sticky-top" style="top: 20px; border-radius: 15px;">
                <div class="card-header bg-success text-white py-2 px-3 small fw-bold text-uppercase tracking-wider">
                    <i class="fas fa-list-check me-2"></i> Bills to Settle
                </div>

                <div class="card-body p-2" id="customerInfoContent" style="max-height: 80vh; overflow-y: auto;">
                    <div class="text-center text-muted py-3 small">
                        Select a customer account to view outstanding bills.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    console.log('Voucher creation script initialized');
    const accounts = @json($accounts);
    const voucherType = "{{ $type }}";
    const defaultCashId = "{{ $cashAccount->id ?? '' }}";
    const defaultBankId = "{{ $bankAccount->id ?? '' }}";

    let rowCount = 0;

    function addRow(data = { account_id: '', debit: 0, credit: 0 }) {
        const rowId = rowCount++;
        const html = `
            <tr id="row-${rowId}" class="animate__animated animate__fadeIn">
                <td>
                    <select name="entries[${rowId}][account_id]" id="select-${rowId}" class="form-select account-select border-0 bg-light rounded-3" required>
                        <option value="">Select Account</option>
                        ${accounts.map(acc => `<option value="${acc.id}" ${data.account_id == acc.id ? 'selected' : ''}>${acc.name} (${acc.type})</option>`).join('')}
                    </select>
                </td>
                <td class="text-end">
                    <input type="number" step="0.01" name="entries[${rowId}][debit]" class="form-control text-end border-0 bg-light rounded-3 debit-input" value="${data.debit}" min="0" oninput="updateTotals()">
                </td>
                <td class="text-end">
                    <input type="number" step="0.01" name="entries[${rowId}][credit]" class="form-control text-end border-0 bg-light rounded-3 credit-input" value="${data.credit}" min="0" oninput="updateTotals()">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-light rounded-circle text-danger" onclick="removeRow(${rowId})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('entriesBody').insertAdjacentHTML('beforeend', html);
        
        // Initialize Select2 for the new row
        $(`#select-${rowId}`).select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#voucherForm')
        });

        updateTotals();
    }

    function removeRow(id) {
        if (document.querySelectorAll('#entriesBody tr').length > 2) {
            document.getElementById(`row-${id}`).remove();
            updateTotals();
        } else {
            alert('At least two entries are required for a double-entry transaction.');
        }
    }

    function updateTotals() {
        let debits = 0;
        let credits = 0;
        document.querySelectorAll('.debit-input').forEach(input => {
            const val = parseFloat(input.value || 0);
            debits += val;
        });
        document.querySelectorAll('.credit-input').forEach(input => {
            const val = parseFloat(input.value || 0);
            credits += val;
        });

        document.getElementById('totalDebit').innerText = debits.toLocaleString(undefined, { minimumFractionDigits: 2 });
        document.getElementById('totalCredit').innerText = credits.toLocaleString(undefined, { minimumFractionDigits: 2 });

        const diff = Math.abs(debits - credits);
        const alertBox = document.getElementById('diffAlert');
        const submitBtn = document.getElementById('submitBtn');

        if (diff > 0.01) {
            alertBox.classList.remove('d-none');
            document.getElementById('diffAmount').innerText = diff.toLocaleString(undefined, { minimumFractionDigits: 2 });
            submitBtn.disabled = true;
            submitBtn.title = "Voucher must be balanced (Debit = Credit)";
        } else if (debits <= 0) {
            alertBox.classList.add('d-none');
            submitBtn.disabled = true;
            submitBtn.title = "Transaction amount must be greater than zero";
        } else {
            alertBox.classList.add('d-none');
            submitBtn.disabled = false;
            submitBtn.title = "";
        }
    }

    // Customer Outstanding Bills Logic
    const billsUrl = "{{ route('payments.get_outstanding_bills') }}";
    const customerSidebar = document.getElementById('customerInfoSidebar');
    const customerSidebarContent = document.getElementById('customerInfoContent');

    function fetchCustomerBills(accountId) {
        const account = accounts.find(a => a.id == accountId);
        if (!account || account.type !== 'customer' || !account.customer_id) {
            if (customerSidebar) customerSidebar.classList.add('d-none');
            // Check if ANY other row has a customer
            const otherCustomerAcc = Array.from(document.querySelectorAll('.account-select'))
                .map(s => accounts.find(a => a.id == s.value))
                .find(a => a && a.type === 'customer' && a.customer_id);
            
            if (otherCustomerAcc) {
                fetchCustomerBills(otherCustomerAcc.id);
            }
            return;
        }

        if (customerSidebar) customerSidebar.classList.remove('d-none');
        if (customerSidebarContent) customerSidebarContent.innerHTML = '<div class="text-center py-3 small text-muted">Loading bills...</div>';

        fetch(`${billsUrl}?customer_id=${account.customer_id}`)
            .then(res => res.json())
            .then(bills => {
                if (bills.length === 0) {
                    if (customerSidebarContent) customerSidebarContent.innerHTML = '<div class="text-center py-3 small text-muted">No outstanding bills.</div>';
                    return;
                }

                let totalOutstanding = bills.reduce((sum, bill) => sum + parseFloat(bill.outstanding), 0);

                let html = `
                    <div class="mb-3 p-2 bg-light rounded-3 text-center border">
                        <div class="text-muted small text-uppercase">Total Outstanding</div>
                        <div class="h5 mb-0 fw-bold text-primary">Rs. ${totalOutstanding.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                    </div>
                    <div class="fw-bold mb-2 border-bottom pb-1 small text-success">${account.name}</div>
                `;
                html += '<div class="list-group list-group-flush small">';

                bills.forEach(bill => {
                    html += `
                        <div class="list-group-item px-3 py-3 border-0 border-bottom-soft">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark h6 mb-0">${bill.bill_number}</span>
                                <span class="small text-muted">${bill.bill_date ? bill.bill_date.split('T')[0] : ''}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="text-muted small">Outstanding Balance</span>
                                <span class="fw-bold text-danger">Rs. ${parseFloat(bill.outstanding).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                if (customerSidebarContent) customerSidebarContent.innerHTML = html;
            })
            .catch(() => {
                if (customerSidebarContent) customerSidebarContent.innerHTML = '<div class="text-center py-3 small text-danger">Error loading bills.</div>';
            });
    }


    // Attach listener to individual select
    $(document).on('change', '.account-select', function() {
        const accountId = $(this).val();
        fetchCustomerBills(accountId);
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Toggle bank details based on payment mode
        const paymentModeSelect = document.getElementById('payment_mode');
        const bankDetailsRow = document.getElementById('bank_details_row');
        
        if (paymentModeSelect) {
            paymentModeSelect.addEventListener('change', function() {
                if (this.value === 'bank') {
                    bankDetailsRow.classList.remove('d-none');
                } else {
                    bankDetailsRow.classList.add('d-none');
                }
            });
        }

        const prefillAccountId = "{{ $prefillAccount->id ?? '' }}";
        const prefillAmount = parseFloat("{{ $prefillAmount ?? 0 }}");

        if (voucherType === 'PV') {
            // DR: The recipient (Agent/User account)
            // CR: Cash/Bank
            addRow({ account_id: prefillAccountId, debit: prefillAmount, credit: 0 });
            addRow({ account_id: defaultCashId, debit: 0, credit: prefillAmount });
        } else if (voucherType === 'RV') {
            // DR: Cash/Bank
            // CR: The source (Customer account)
            addRow({ account_id: defaultCashId || defaultBankId, debit: prefillAmount, credit: 0 });
            addRow({ account_id: prefillAccountId, debit: 0, credit: prefillAmount });
            
            if (prefillAccountId) {
                fetchCustomerBills(prefillAccountId);
            }
        } else {
            addRow({ account_id: prefillAccountId, debit: prefillAmount, credit: 0 });
            addRow();
        }
    });

</script>

<style>
    .font-weight-bold { font-weight: 700 !important; }
    .bg-light { background-color: #f8f9fc !important; }
    .alert-soft-danger { background-color: #ffe8e8; border: none; color: #dc3545; }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(66, 133, 244, 0.1);
    }
</style>
@endpush
