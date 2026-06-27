@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<h1>Record Payment</h1>
<form method="POST" action="{{ route('payments.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-3">
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
                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
            </div>

            <div class="mb-3">
                <label for="payment_party_id" class="form-label">Collection Account / Partner <span class="text-danger">*</span></label>
                <select class="form-select select2" id="payment_party_id" name="payment_party_id" required>
                    <option value="">Select Account</option>
                    @foreach($paymentParties as $party)
                        <option value="{{ $party->id }}" {{ old('payment_party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="destination_type" value="cash_bank"> <!-- Standardized for internal logic -->
            </div>



            <div class="mb-3">
                <label for="mode" class="form-label">Mode of Payment</label>
                <select class="form-control" id="mode" name="mode" required>
                    <option value="cash" {{ old('mode') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ old('mode') === 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="upi" {{ old('mode') === 'upi' ? 'selected' : '' }}>UPI</option>
                    <option value="other" {{ old('mode') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Payment Amount</label>
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white border-primary">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                    <input type="text" class="form-control border-primary fw-bold" id="total_amount" name="total_amount" value="0.00">
                </div>
                <!-- Large Formatted Display -->
                <div class="h5 mt-2 font-weight-bold text-primary" id="total-formatted">{{ $companySetting->currency_symbol ?? 'Rs.' }} 0.00</div>
                <div class="small text-muted mb-2 fw-bold" id="amount-in-words" style="font-style: italic;">Rupees Zero Only</div>
                <small class="text-muted">Enter total amount to auto-distribute or select bills below.</small>
            </div>

            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Enter payee or additional details">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="col-lg-9">
            <div id="bill-payments-section" class="mb-3 d-none">
                <label class="form-label h5 mb-3"><i class="fas fa-list-check me-2"></i>Bills to settle</label>
                @error('bill_payments')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror
                <div id="bill-payments-loader" class="alert alert-info py-2 px-3 d-none">Loading outstanding bills…</div>
                <div id="bill-payments-empty" class="alert alert-warning py-2 px-3 d-none">No outstanding bills for this customer.</div>
                <div class="table-responsive border rounded">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead>
                            <tr class="table-dark">
                                <th style="width: 55px;" class="text-center">Select</th>
                                <th>Bill #</th>
                                <th>Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Outstanding</th>
                                <th style="width: 180px;">Payment Amount</th>
                            </tr>
                        </thead>
                        <tbody id="bill-payments-body"></tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-center">GRAND TOTAL</td>
                                <td class="text-end" id="total-bill-sum">0.00</td>
                                <td class="text-end" id="total-paid-sum">0.00</td>
                                <td class="text-end" id="total-outstanding-sum">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="text-muted small mt-2">Select each bill being settled and enter the amount received against it. Leave unchecked bills blank.</div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Record Payment</button>
    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
</form>

<script>
const currencySymbol = "{{ $companySetting->currency_symbol ?? 'Rs.' }}";
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
    updateAmountInWords();
};

const formatInput = (input) => {
    const selectionStart = input.selectionStart;
    const value = input.value;
    
    // Count digits before cursor
    let digitsBeforeCursor = 0;
    for (let i = 0; i < selectionStart; i++) {
        if (/[0-9.]/.test(value[i])) {
            digitsBeforeCursor++;
        }
    }
    
    // Clean and parse the whole value
    let cleanVal = value.replace(/[^0-9.]/g, '');
    
    // Handle decimal points (keep only first)
    const dotIndex = cleanVal.indexOf('.');
    if (dotIndex !== -1) {
        cleanVal = cleanVal.substring(0, dotIndex + 1) + cleanVal.substring(dotIndex + 1).replace(/\./g, '');
        const parts = cleanVal.split('.');
        if (parts[1].length > 2) {
            parts[1] = parts[1].substring(0, 2);
        }
        cleanVal = parts.join('.');
    }
    
    // Format integer part
    const parts = cleanVal.split('.');
    let integerPart = parts[0];
    const decimalPart = parts[1];
    
    if (integerPart.length > 1 && integerPart.startsWith('0')) {
        integerPart = integerPart.replace(/^0+/, '');
        if (integerPart === '') integerPart = '0';
    }
    
    const formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    let formattedValue = formattedInteger;
    if (dotIndex !== -1) {
        formattedValue += '.' + (decimalPart !== undefined ? decimalPart : '');
    }
    
    input.value = formattedValue;
    
    // Position cursor after the same number of digits
    let newCursorPos = 0;
    let digitsFound = 0;
    for (let i = 0; i < formattedValue.length; i++) {
        if (digitsFound === digitsBeforeCursor) {
            break;
        }
        if (/[0-9.]/.test(formattedValue[i])) {
            digitsFound++;
        }
        newCursorPos++;
    }
    
    input.setSelectionRange(newCursorPos, newCursorPos);
};

const formatOnBlur = (input) => {
    let cleanVal = input.value.replace(/[^0-9.]/g, '');
    let parsed = parseFloat(cleanVal);
    if (isNaN(parsed)) {
        parsed = 0;
    }
    input.value = parsed.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const convertNumberToWords = (number, isFinal = true) => {
    const dictionary = {
        0: 'zero', 1: 'one', 2: 'two', 3: 'three', 4: 'four', 5: 'five',
        6: 'six', 7: 'seven', 8: 'eight', 9: 'nine', 10: 'ten',
        11: 'eleven', 12: 'twelve', 13: 'thirteen', 14: 'fourteen', 15: 'fifteen',
        16: 'sixteen', 17: 'seventeen', 18: 'eighteen', 19: 'nineteen', 20: 'twenty',
        30: 'thirty', 40: 'forty', 50: 'fifty', 60: 'sixty', 70: 'seventy',
        80: 'eighty', 90: 'ninety', 100: 'hundred', 1000: 'thousand',
        1000000: 'million', 1000000000: 'billion'
    };

    if (isNaN(number)) return '';
    if (number < 0) return 'negative ' + convertNumberToWords(Math.abs(number), isFinal);

    let string = null;

    if (number < 21) {
        string = dictionary[number];
    } else if (number < 100) {
        const tens = Math.floor(number / 10) * 10;
        const units = number % 10;
        string = dictionary[tens];
        if (units) {
            string += '-' + dictionary[units];
        }
    } else if (number < 1000) {
        const hundreds = Math.floor(number / 100);
        const remainder = number % 100;
        string = dictionary[hundreds] + ' ' + dictionary[100];
        if (remainder) {
            string += (isFinal ? ' and ' : ' ') + convertNumberToWords(remainder, isFinal);
        }
    } else {
        let baseUnit = 1000;
        if (number >= 1000000000) {
            baseUnit = 1000000000;
        } else if (number >= 1000000) {
            baseUnit = 1000000;
        }

        const numBaseUnits = Math.floor(number / baseUnit);
        const remainder = number % baseUnit;

        string = convertNumberToWords(numBaseUnits, false) + ' ' + dictionary[baseUnit];

        if (remainder) {
            string += remainder < 100 ? ' and ' : ' ';
            string += convertNumberToWords(remainder, isFinal);
        }
    }

    return string;
};

const amountToWords = (number) => {
    if (isNaN(number) || number <= 0) {
        let currencyName = currencySymbol;
        if (currencyName === 'Rs.') {
            currencyName = 'Rupees';
        }
        return currencyName + " Zero Only";
    }
    const whole = Math.floor(number);
    const decimal = Math.round((number - whole) * 100);

    const words = convertNumberToWords(whole);
    const titleCase = (str) => str.replace(/\b\w/g, c => c.toUpperCase());

    let paisa = "";
    if (decimal > 0) {
        paisa = " and " + titleCase(convertNumberToWords(decimal)) + " Paise";
    }

    let currencyName = currencySymbol;
    if (currencyName === 'Rs.') {
        currencyName = 'Rupees';
    }

    return currencyName + " " + titleCase(words) + paisa + " Only";
};

const updateAmountInWords = () => {
    const valString = totalAmountInput.value.replace(/,/g, '');
    const amount = parseFloat(valString);
    document.getElementById('amount-in-words').textContent = amountToWords(amount);
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
    const formattedTotal = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    totalAmountInput.value = formattedTotal;
    document.getElementById('total-formatted').textContent = currencySymbol + ' ' + formattedTotal;
    updateAmountInWords();
};

const distributeTotal = () => {
    let remaining = parseFloat(totalAmountInput.value.replace(/,/g, ''));
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
    formatInput(totalAmountInput);
    // Only distribute if the user is typing (not if it was updated by updateTotal)
    if (document.activeElement === totalAmountInput) {
        distributeTotal();
    }
    updateAmountInWords();
});

totalAmountInput.addEventListener('blur', (e) => {
    formatOnBlur(totalAmountInput);
    updateAmountInWords();
});

const renderBills = (bills) => {
    billTableBody.innerHTML = '';
    
    // Reset totals
    let totalBillSum = 0;
    let totalPaidSum = 0;
    let totalOutstandingSum = 0;

    if (!bills.length) {
        billEmpty.textContent = 'No outstanding bills for this customer.';
        billEmpty.classList.remove('d-none');
        document.getElementById('total-bill-sum').textContent = '0.00';
        document.getElementById('total-paid-sum').textContent = '0.00';
        document.getElementById('total-outstanding-sum').textContent = '0.00';
        updateTotal();
        return;
    }

    billEmpty.classList.add('d-none');

    bills.forEach(bill => {
        const row = document.createElement('tr');
        const billTotal = parseFloat(bill.total);
        const billPaid = parseFloat(bill.paid);
        const outstanding = parseFloat(bill.outstanding);
        
        totalBillSum += billTotal;
        totalPaidSum += billPaid;
        totalOutstandingSum += outstanding;

        const oldAmount = oldBillPayments && Object.prototype.hasOwnProperty.call(oldBillPayments, bill.id)
            ? parseFloat(oldBillPayments[bill.id])
            : null;
        const isSelected = oldAmount && !isNaN(oldAmount) && oldAmount > 0;

        row.innerHTML = `
            <td class="text-center">
                <input type="checkbox" class="form-check-input bill-select" data-bill="${bill.id}">
            </td>
            <td>${bill.bill_number ?? `BILL-${bill.id}`}</td>
            <td>${bill.bill_date ? bill.bill_date.split('T')[0] : '-'}</td>
            <td class="text-end">${billTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end">${billPaid.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end text-primary fw-bold font-monospace">${outstanding.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
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

    // Update tfoot totals
    document.getElementById('total-bill-sum').textContent = totalBillSum.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('total-paid-sum').textContent = totalPaidSum.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('total-outstanding-sum').textContent = totalOutstandingSum.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

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

// Initial formatting and words display
formatOnBlur(totalAmountInput);
updateAmountInWords();
</script>
@endsection
