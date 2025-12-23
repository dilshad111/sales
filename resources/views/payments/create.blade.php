@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<h1>Record Payment</h1>
<form method="POST" action="{{ route('payments.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="customer_id" class="form-label">Customer</label>
                <select class="form-control" id="customer_id" name="customer_id" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
            </div>

            <div class="mb-3">
                <label for="payment_party_id" class="form-label">Payment Party</label>
                <select class="form-control" id="payment_party_id" name="payment_party_id">
                    <option value="">Select Payment Party</option>
                    @foreach($paymentParties as $party)
                        <option value="{{ $party->id }}" {{ old('payment_party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                    @endforeach
                </select>
                @error('payment_party_id')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="mode" class="form-label">Mode of Payment</label>
                <select class="form-control" id="mode" name="mode" required>
                    <option value="cash" {{ old('mode') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ old('mode') === 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="other" {{ old('mode') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="mb-3 mb-lg-0">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter payee or additional details">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Payment Amount</label>
                <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" value="0.00">
                <small class="text-muted">Enter total amount to auto-distribute or select bills below.</small>
            </div>

            <div id="bill-payments-section" class="mb-3 d-none">
                <label class="form-label">Bills to pay</label>
                @error('bill_payments')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror
                <div id="bill-payments-loader" class="alert alert-info py-2 px-3 d-none">Loading outstanding bills…</div>
                <div id="bill-payments-empty" class="alert alert-warning py-2 px-3 d-none">No outstanding bills for this customer.</div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr class="table-light">
                                <th style="width: 55px;">Select</th>
                                <th>Bill #</th>
                                <th>Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Outstanding</th>
                                <th style="width: 180px;">Payment Amount</th>
                            </tr>
                        </thead>
                        <tbody id="bill-payments-body"></tbody>
                    </table>
                </div>
                <div class="text-muted small">Select each bill being settled and enter the amount received against it. Leave unchecked bills blank.</div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Record Payment</button>
    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
</form>

<script>
const customerSelect = document.getElementById('customer_id');
const billSection = document.getElementById('bill-payments-section');
const billTableBody = document.getElementById('bill-payments-body');
const billLoader = document.getElementById('bill-payments-loader');
const billEmpty = document.getElementById('bill-payments-empty');
const totalAmountInput = document.getElementById('total_amount');
const billsUrl = @json(route('payments.get_outstanding_bills'));
const oldBillPayments = @json(old('bill_payments', []));

const resetBillSection = () => {
    billTableBody.innerHTML = '';
    billLoader.classList.add('d-none');
    billEmpty.classList.add('d-none');
    totalAmountInput.value = '0.00';
};

const updateTotal = () => {
    let total = 0;
    billTableBody.querySelectorAll('input.bill-amount').forEach(input => {
        if (!input.disabled) {
            const value = parseFloat(input.value);
            if (!isNaN(value)) {
                total += value;
            }
        }
    });
    totalAmountInput.value = total.toFixed(2);
};

const distributeTotal = () => {
    let remaining = parseFloat(totalAmountInput.value);
    if (isNaN(remaining) || remaining < 0) remaining = 0;

    billTableBody.querySelectorAll('tr').forEach(row => {
        const checkbox = row.querySelector('.bill-select');
        const amountInput = row.querySelector('.bill-amount');
        const maxInput = parseFloat(amountInput.max);

        if (remaining > 0) {
            checkbox.checked = true;
            amountInput.disabled = false;
            const toApply = Math.min(remaining, maxInput);
            amountInput.value = toApply.toFixed(2);
            remaining -= toApply;
        } else {
            checkbox.checked = false;
            amountInput.disabled = true;
            amountInput.value = '';
        }
    });
};

totalAmountInput.addEventListener('input', (e) => {
    // Only distribute if the user is typing (not if it was updated by updateTotal)
    if (document.activeElement === totalAmountInput) {
        distributeTotal();
    }
});

const renderBills = (bills) => {
    billTableBody.innerHTML = '';

    if (!bills.length) {
        billEmpty.textContent = 'No outstanding bills for this customer.';
        billEmpty.classList.remove('d-none');
        updateTotal();
        return;
    }

    billEmpty.classList.add('d-none');

    bills.forEach(bill => {
        const row = document.createElement('tr');
        const outstanding = parseFloat(bill.outstanding);
        const oldAmount = oldBillPayments && Object.prototype.hasOwnProperty.call(oldBillPayments, bill.id)
            ? parseFloat(oldBillPayments[bill.id])
            : null;
        const isSelected = oldAmount && !isNaN(oldAmount) && oldAmount > 0;

        row.innerHTML = `
            <td class="text-center">
                <input type="checkbox" class="form-check-input bill-select" data-bill="${bill.id}">
            </td>
            <td>${bill.bill_number ?? `BILL-${bill.id}`}</td>
            <td>${bill.bill_date ?? '-'}</td>
            <td class="text-end">${Number(bill.total).toFixed(2)}</td>
            <td class="text-end">${Number(bill.paid).toFixed(2)}</td>
            <td class="text-end">${outstanding.toFixed(2)}</td>
            <td>
                <input type="number" step="0.01" min="0.01" max="${outstanding.toFixed(2)}" class="form-control form-control-sm bill-amount" name="bill_payments[${bill.id}]" placeholder="Up to ${outstanding.toFixed(2)}">
            </td>
        `;

        const checkbox = row.querySelector('.bill-select');
        const amountInput = row.querySelector('.bill-amount');

        if (isSelected) {
            checkbox.checked = true;
            amountInput.value = oldAmount.toFixed(2);
            amountInput.disabled = false;
        } else {
            amountInput.disabled = true;
        }

        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                amountInput.disabled = false;
                if (!amountInput.value) {
                    amountInput.value = outstanding.toFixed(2);
                }
            } else {
                amountInput.value = '';
                amountInput.disabled = true;
            }
            updateTotal();
        });

        amountInput.addEventListener('input', updateTotal);

        billTableBody.appendChild(row);
    });

    updateTotal();
};

const loadBillsForCustomer = (customerId) => {
    resetBillSection();

    if (!customerId) {
        billSection.classList.add('d-none');
        return;
    }

    billSection.classList.remove('d-none');
    billLoader.textContent = 'Loading outstanding bills…';
    billLoader.classList.remove('d-none');

    fetch(`${billsUrl}?customer_id=${encodeURIComponent(customerId)}`)
        .then(response => response.json())
        .then(data => {
            billLoader.classList.add('d-none');
            renderBills(data);
        })
        .catch(() => {
            billLoader.classList.add('d-none');
            billEmpty.textContent = 'Unable to load outstanding bills. Please try again.';
            billEmpty.classList.remove('d-none');
        });
};

customerSelect.addEventListener('change', (event) => {
    loadBillsForCustomer(event.target.value);
});

if (customerSelect.value) {
    loadBillsForCustomer(customerSelect.value);
}
</script>
@endsection
