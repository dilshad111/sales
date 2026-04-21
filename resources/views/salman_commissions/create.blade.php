@extends('layouts.app')

@section('title', 'Generate Commission')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-plus-circle me-2 text-primary"></i>Generate Commission</h1>
    <a href="{{ route('salman_commissions.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm me-1"></i>Back to List
    </a>
</div>

<form action="{{ route('salman_commissions.store') }}" method="POST" id="commissionForm">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Commission Header</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-bold">Select Person (Commissionee)</label>
                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ ($salman && $salman->id == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_id" class="form-label fw-bold">Select Customer</label>
                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="commission_date" class="form-label fw-bold">Commission Bill Date</label>
                        <input type="date" name="commission_date" id="commission_date" class="form-control @error('commission_date') is-invalid @enderror" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        @error('commission_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">Notes / Remarks</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="border-top pt-3 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-bold">Total Selected:</span>
                            <span id="selectedCount" class="h5 mb-0 fw-bold">0 Bills</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Total Commission:</span>
                            <span id="totalCommission" class="h4 mb-0 fw-bold text-success">{{ $companySetting->currency_symbol ?? 'Rs.' }} 0.00</span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-4 py-2 fw-bold" id="submitBtn" disabled>
                            <i class="fas fa-save me-1"></i>Generate Commission Bill
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Bills Selection</h6>
                    <div id="loadingBills" style="display:none;" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle" id="billsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">
                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                    </th>
                                    <th>Bill #</th>
                                    <th>Date</th>
                                    <th class="text-end">Bill Amount</th>
                                    <th class="text-center" style="width: 150px;">Comm. %</th>
                                    <th class="text-end" style="width: 150px;">Comm. Amt</th>
                                </tr>
                            </thead>
                            <tbody id="billsList">
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-user-tag fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">Please select a customer to load bills.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
const currencySymbol = "{{ $companySetting->currency_symbol ?? 'Rs.' }}";

document.getElementById('customer_id').addEventListener('change', function() {
    const customerId = this.value;
    const billsList = document.getElementById('billsList');
    const loading = document.getElementById('loadingBills');

    if (!customerId) {
        billsList.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Please select a customer to load bills.</td></tr>';
        return;
    }

    loading.style.display = 'inline-block';
    
    fetch(`{{ route('salman_commissions.get_customer_bills') }}?customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            if (data.length === 0) {
                billsList.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">No bills found for this customer.</td></tr>';
                return;
            }

            let html = '';
            data.forEach((bill, index) => {
                const disabledRow = bill.is_commissioned ? 'disabled' : '';
                const commissionedText = bill.is_commissioned ? `<div class="text-danger small mt-1"><i class="fas fa-exclamation-circle"></i> Already Paid</div>` : '';
                const rowClass = bill.is_commissioned ? 'table-light opacity-50' : '';

                html += `
                <tr class="${rowClass}">
                    <td class="ps-3">
                        <input type="checkbox" name="bills[${index}][id]" value="${bill.id}" class="form-check-input bill-checkbox" ${disabledRow}>
                    </td>
                    <td>
                        <div class="fw-bold">${bill.bill_number}</div>
                        ${commissionedText}
                    </td>
                    <td>${bill.bill_date}</td>
                    <td class="text-end">${currencySymbol} ${parseFloat(bill.total).toLocaleString()}</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="number" name="bills[${index}][percent]" class="form-control comm-percent" value="0" step="0.01" min="0" max="100" disabled>
                            <span class="input-group-text">%</span>
                        </div>
                    </td>
                    <td class="text-end fw-bold text-dark comm-amount-display">${currencySymbol} 0.00</td>
                </tr>
                `;
            });
            billsList.innerHTML = html;
            
            // Add change listener to newly created checkboxes to enable/disable percent input
            document.querySelectorAll('.bill-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const percentInput = row.querySelector('.comm-percent');
                    percentInput.disabled = !this.checked;
                    updateTotals();
                });
            });

            updateTotals();
        });
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('bill-checkbox') || e.target.classList.contains('comm-percent') || e.target.id === 'checkAll') {
        if (e.target.id === 'checkAll') {
            document.querySelectorAll('.bill-checkbox:not(:disabled)').forEach(cb => cb.checked = e.target.checked);
        }
        updateTotals();
    }
});

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('comm-percent')) {
        updateTotals();
    }
});

function updateTotals() {
    let total = 0;
    let count = 0;
    const billsRows = document.querySelectorAll('#billsList tr');
    
    billsRows.forEach(row => {
        const checkbox = row.querySelector('.bill-checkbox');
        if (checkbox && checkbox.checked) {
            count++;
            const billAmountText = row.children[3].innerText.replace(new RegExp(currencySymbol.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s|,', 'g'), '');
            const billAmount = parseFloat(billAmountText);
            const percentInput = row.querySelector('.comm-percent');
            const percent = parseFloat(percentInput.value) || 0;
            
            const commAmt = (billAmount * percent) / 100;
            total += commAmt;
            
            row.querySelector('.comm-amount-display').innerText = `${currencySymbol} ${commAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        } else if (checkbox) {
            row.querySelector('.comm-amount-display').innerText = `${currencySymbol} 0.00`;
        }
    });

    document.getElementById('totalCommission').innerText = `${currencySymbol} ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    document.getElementById('selectedCount').innerText = `${count} Bills`;
    document.getElementById('submitBtn').disabled = (count === 0);
}
</script>
@endsection
