@extends('layouts.app')

@section('title', 'Create Direct Sale Invoice')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h3 class="mb-0 fw-bold">
        <span class="text-muted fw-light">Direct /</span> Create Sale Invoice
    </h3>
    <a href="{{ route('bills.index') }}" class="btn btn-label-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to List
    </a>
</div>

<div class="card shadow-none border">
    <div class="card-body">
<link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" />
<form id="billForm" method="POST" action="{{ route('bills.store') }}">
    @csrf


    <!-- Header Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-label-primary p-2 rounded me-2">
                    <i class="fas fa-hashtag"></i>
                </div>
                <label for="bill_number" class="form-label mb-0 fw-semibold text-uppercase small ls-1">Invoice Number</label>
            </div>
            <input type="text" class="form-control bg-light fw-bold" id="bill_number" value="{{ $nextBillNumber }}" disabled style="font-family: 'Outfit', sans-serif;">
            <div class="form-text mt-1" style="font-size: 0.75rem;">System generated reference</div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-label-info p-2 rounded me-2">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <label for="bill_date" class="form-label mb-0 fw-semibold text-uppercase small ls-1">Invoice Date</label>
            </div>
            <input type="date" class="form-control" id="bill_date" name="bill_date" required value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-label-warning p-2 rounded me-2">
                    <i class="fas fa-user-tie"></i>
                </div>
                <label for="customer_id" class="form-label mb-0 fw-semibold text-uppercase small ls-1">Select Customer</label>
            </div>
            <select class="form-select select2" id="customer_id" name="customer_id" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Address Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-light p-3 rounded-3 border-start border-primary border-4">
                <label for="customer_address" class="form-label fw-bold text-muted small text-uppercase mb-2">
                    <i class="fas fa-map-marker-alt me-1"></i> Billing Address
                </label>
                <textarea class="form-control border-0 bg-transparent p-0 fs-6 fw-medium" id="customer_address" name="customer_address" rows="1" readonly placeholder="Customer address will appear here..."></textarea>
            </div>
        </div>
    </div>

    <!-- Items Section -->
    <div class="row g-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Invoice Items</h4>
            <div class="card shadow-none border-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle table-sm" id="itemsTable">
                        <thead>
                            <tr>
                                <th class="ps-3" style="min-width:280px;">Item Description</th>
                                <th class="text-center" style="width:90px;">Qty</th>
                                <th class="text-center" style="width:110px;">Rate</th>
                                <th class="text-center" style="width:120px;">Total</th>
                                <th class="text-center" style="width:140px;">Delivery</th>
                                <th class="text-center" style="width:200px;">Remarks</th>
                                <th class="text-center" style="width:80px;">Action</th>
                            </tr>
                        </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td style="min-width:280px;">
                                <select class="form-select form-select-sm item-select" name="items[0][item_id]" required>
                                    <option value="">Select Item</option>
                                </select>
                            </td>
                            <td style="width:90px;">
                                <input type="text" class="form-control form-control-sm text-end qty-input" name="items[0][quantity]" placeholder="0" required>
                            </td>
                            <td style="width:110px;">
                                <input type="text" class="form-control form-control-sm text-end rate-input" name="items[0][rate]" placeholder="0.00" required>
                            </td>
                            <td style="width:120px;">
                                <input type="text" class="form-control form-control-sm text-end total-input" value="0.00" readonly>
                            </td>
                            <td style="width:140px;">
                                <input type="text" class="form-control form-control-sm delivery-date-input" name="items[0][delivery_date]" placeholder="dd/mm/yyyy" autocomplete="off">
                            </td>
                            <td style="width:200px;">
                                <input type="text" class="form-control form-control-sm remarks-input" name="items[0][remarks]" placeholder="Remarks">
                            </td>
                            <td class="text-center" style="width:80px;">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item" disabled><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td class="text-end">Total:</td>
                            <td class="text-end text-primary" id="totalQtyDisplay">0</td>
                            <td></td>
                            <td class="text-end text-primary" id="totalAmountDisplay">0.00</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" id="addItem" class="btn btn-secondary btn-sm mt-2"><i class="fas fa-plus me-1"></i>Add Item</button>

            {{-- Additional Expenses Section --}}
            <div class="mt-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2 text-warning"></i>Additional Expenses</h5>
                    <button type="button" id="addExpense" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Additional Expense
                    </button>
                </div>
                <div id="expensesContainer">
                    {{-- rows injected by JS --}}
                </div>
            </div>

            <div class="row justify-content-end mt-4">
                <div class="col-md-6 col-lg-5">
                    <table class="table table-sm mb-0">
                        <tbody class="border-top-0">
                            <tr class="border-bottom-0">
                                <th class="text-muted fw-normal py-2">Sub Total</th>
                                <td class="text-end fw-bold py-2"><span id="subTotal">0.00</span></td>
                            </tr>
                            <tr class="border-bottom-0">
                                <th class="text-muted fw-normal py-2">Discount</th>
                                <td class="py-1">
                                    <input type="text" class="form-control form-control-sm text-end currency-input border-dashed" id="discount" name="discount" value="0.00">
                                </td>
                            </tr>
                            <tr class="border-bottom-0">
                                <th class="text-muted fw-normal py-2">Tax (%)</th>
                                <td class="py-1">
                                    <input type="text" class="form-control form-control-sm text-end border-dashed" id="tax_percent" name="tax_percent" value="0.00">
                                </td>
                            </tr>
                            <tr class="border-bottom-0">
                                <th class="text-muted fw-normal py-2">Tax Amount</th>
                                <td class="text-end fw-bold py-2">
                                    <input type="hidden" id="tax" name="tax" value="0.00">
                                    <span id="taxAmountDisplay">0.00</span>
                                </td>
                            </tr>
                            <tr id="expensesSummaryRow" style="display:none;" class="border-bottom-0">
                                <th class="text-muted fw-normal py-2">Expenses</th>
                                <td class="text-end fw-bold py-2"><span id="expensesTotal">0.00</span></td>
                            </tr>
                            <tr class="bg-label-primary border-top border-primary border-2">
                                <th class="fw-bold py-3 fs-5">Grand Total</th>
                                <td class="text-end fw-bold py-3 fs-5 text-primary">
                                    <span class="currency-symbol small me-1">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                                    <span id="grandTotal">0.00</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 border-top pt-4">
        <button type="submit" class="btn btn-primary btn-lg shadow-sm"><i class="fas fa-save me-1"></i>Create Invoice</button>
        <a href="{{ route('bills.index') }}" class="btn btn-label-secondary btn-lg ms-2"><i class="fas fa-times me-1"></i>Cancel</a>
    </div>
</form>
</div>
</div>

@push('scripts')
<!-- Local script for Flatpickr -->
<script src="{{ asset('js/flatpickr.min.js') }}"></script>
<script>
(() => {
    const parseNumber = (value) => {
        if (value === undefined || value === null) {
            return 0;
        }
        const numeric = value.toString().replace(/,/g, '').trim();
        const parsed = parseFloat(numeric);
        return Number.isNaN(parsed) ? 0 : parsed;
    };

    const formatNumber = (value, decimals = 0) => {
        const number = parseNumber(value);
        return number.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });
    };

    const formatCurrency = (value) => formatNumber(value, 2);
    const formatQuantity = (value) => formatNumber(value, 0);

    const itemsBody = document.getElementById('itemsBody');
    const addItemBtn = document.getElementById('addItem');
    const discountInput = document.getElementById('discount');
    const taxPercentInput = document.getElementById('tax_percent');
    const taxHiddenInput = document.getElementById('tax');
    const taxDisplay = document.getElementById('taxAmountDisplay');
    const subTotalEl = document.getElementById('subTotal');
    const grandTotalEl = document.getElementById('grandTotal');
    let itemIndex = itemsBody.querySelectorAll('.item-row').length;

    const refreshRemoveButtons = () => {
        const rows = itemsBody.querySelectorAll('.item-row');
        rows.forEach((row) => {
            const removeBtn = row.querySelector('.remove-item');
            removeBtn.disabled = rows.length === 1;
        });
    };

    const updateRowTotal = (row) => {
        if (!row) {
            return;
        }
        const quantityInput = row.querySelector('.qty-input');
        const rateInput = row.querySelector('.rate-input');
        const totalInput = row.querySelector('.total-input');

        const quantity = parseNumber(quantityInput.value);
        const rate = parseNumber(rateInput.value);
        const total = quantity * rate;

        totalInput.value = formatCurrency(total);
    };

    const expensesContainer = document.getElementById('expensesContainer');
    const expensesSummaryRow = document.getElementById('expensesSummaryRow');
    const expensesTotalEl = document.getElementById('expensesTotal');
    let expenseIndex = 0;

    const updateTotals = () => {
        let subtotal = 0;
        itemsBody.querySelectorAll('.total-input').forEach((input) => {
            subtotal += parseNumber(input.value);
        });

        let totalQty = 0;
        itemsBody.querySelectorAll('.qty-input').forEach((input) => {
            totalQty += parseNumber(input.value);
        });
        
        const qtyDisplay = document.getElementById('totalQtyDisplay');
        if (qtyDisplay) qtyDisplay.textContent = formatQuantity(totalQty);

        const amountDisplay = document.getElementById('totalAmountDisplay');
        if (amountDisplay) amountDisplay.textContent = formatCurrency(subtotal);

        let expensesSum = 0;
        expensesContainer.querySelectorAll('.expense-amount-input').forEach((input) => {
            expensesSum += parseNumber(input.value);
        });

        const discount = parseNumber(discountInput.value);
        const taxPercent = parseNumber(taxPercentInput.value);
        const taxableAmount = subtotal - discount;
        const taxAmount = taxableAmount * (taxPercent / 100);

        taxHiddenInput.value = taxAmount.toFixed(2);
        if (taxDisplay) taxDisplay.textContent = formatCurrency(taxAmount);

        subTotalEl.textContent = formatCurrency(subtotal);
        expensesTotalEl.textContent = formatCurrency(expensesSum);
        expensesSummaryRow.style.display = expensesSum > 0 ? '' : 'none';
        grandTotalEl.textContent = formatCurrency(subtotal - discount + taxAmount + expensesSum);
    };

    const addExpenseRow = () => {
        const row = document.createElement('div');
        row.className = 'expense-row d-flex gap-2 align-items-center mb-2';
        row.innerHTML = `
            <input type="text" class="form-control" name="expenses[${expenseIndex}][description]" placeholder="Expense Details (e.g. Freight, Labour)" style="flex:2;">
            <input type="text" class="form-control text-end expense-amount-input" name="expenses[${expenseIndex}][amount]" placeholder="0.00" style="flex:1; max-width:180px;">
            <button type="button" class="btn btn-outline-danger btn-sm remove-expense" title="Remove"><i class="fas fa-times"></i></button>
        `;
        expensesContainer.appendChild(row);

        const amountInput = row.querySelector('.expense-amount-input');
        amountInput.addEventListener('input', updateTotals);
        amountInput.addEventListener('focusout', () => {
            amountInput.value = formatCurrency(parseNumber(amountInput.value));
            updateTotals();
        });
        row.querySelector('.remove-expense').addEventListener('click', () => {
            row.remove();
            updateTotals();
        });
        expenseIndex++;
    };

    document.getElementById('addExpense').addEventListener('click', addExpenseRow);

    const handleItemChange = (select) => {
        const row = select.closest('.item-row');
        const rateInput = row.querySelector('.rate-input');
        const price = parseNumber(select.selectedOptions[0]?.dataset.price || 0);

        rateInput.value = price ? formatCurrency(price) : '';
        updateRowTotal(row);
        updateTotals();
    };

    const initDatePicker = (input) => {
        if (!input) {
            return;
        }
        if (input._flatpickr) {
            input._flatpickr.destroy();
        }
        flatpickr(input, {
            dateFormat: 'd/m/Y',
            allowInput: true,
        });
    };

    const initSelect2 = (select) => {
        if (!select) {
            return;
        }
        // Destroy existing Select2 instance if any
        if ($(select).data('select2')) {
            $(select).select2('destroy');
        }
        // Initialize Select2 with search
        $(select).select2({
            theme: 'bootstrap-5',
            placeholder: 'Select Item',
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: true
        });
    };

    const addItemRow = () => {
        const firstRow = itemsBody.querySelector('.item-row');
        
        // Destroy Select2 on the first row before cloning
        const firstSelect = firstRow.querySelector('.item-select');
        if ($(firstSelect).data('select2')) {
            $(firstSelect).select2('destroy');
        }
        
        const template = firstRow.cloneNode(true);
        
        // Reinitialize Select2 on the first row
        initSelect2(firstSelect);
        
        template.querySelectorAll('input, select').forEach((input) => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            }

            if (input.classList.contains('item-select')) {
                input.selectedIndex = 0;
            } else if (input.classList.contains('qty-input')) {
                input.value = '';
            } else if (input.classList.contains('rate-input')) {
                input.value = '';
            } else if (input.classList.contains('delivery-date-input')) {
                input.value = '';
            } else if (input.classList.contains('remarks-input')) {
                input.value = '';
            }
        });

        template.querySelector('.total-input').value = formatCurrency(0);
        template.querySelector('.remove-item').disabled = false;
        itemsBody.appendChild(template);

        initDatePicker(template.querySelector('.delivery-date-input'));
        initSelect2(template.querySelector('.item-select'));

        itemIndex++;
        refreshRemoveButtons();
    };

    itemsBody.addEventListener('change', (event) => {
        if (event.target.classList.contains('item-select')) {
            handleItemChange(event.target);
        }
    });
    
    // Add Select2 change event listener using jQuery delegation
    $(itemsBody).on('select2:select', '.item-select', function(e) {
        handleItemChange(this);
    });

    itemsBody.addEventListener('input', (event) => {
        if (event.target.classList.contains('qty-input') || event.target.classList.contains('rate-input')) {
            updateRowTotal(event.target.closest('.item-row'));
            updateTotals();
        }
    });

    itemsBody.addEventListener('focusout', (event) => {
        if (event.target.classList.contains('qty-input')) {
            const value = parseNumber(event.target.value);
            event.target.value = value ? formatQuantity(value) : '';
            updateRowTotal(event.target.closest('.item-row'));
            updateTotals();
        }

        if (event.target.classList.contains('rate-input')) {
            const value = parseNumber(event.target.value);
            event.target.value = value ? formatCurrency(value) : '';
            updateRowTotal(event.target.closest('.item-row'));
            updateTotals();
        }
    });

    itemsBody.addEventListener('click', (event) => {
        if (event.target.classList.contains('remove-item')) {
            event.preventDefault();
            const rows = itemsBody.querySelectorAll('.item-row');
            if (rows.length > 1) {
                event.target.closest('.item-row').remove();
                refreshRemoveButtons();
                updateTotals();
            }
        }
    });

    addItemBtn.addEventListener('click', () => {
        addItemRow();
    });

    [discountInput, taxPercentInput].forEach((input) => {
        input.addEventListener('input', updateTotals);
        input.addEventListener('focusout', () => {
            if (input === taxPercentInput) {
                input.value = parseNumber(input.value).toFixed(2);
            } else {
                input.value = formatCurrency(parseNumber(input.value));
            }
            updateTotals();
        });
        if (input === taxPercentInput) {
            input.value = parseNumber(input.value).toFixed(2);
        } else {
            input.value = formatCurrency(parseNumber(input.value));
        }
    });

    const loadItemsForCustomer = (customerId) => {
        if (!customerId) return;

        fetch(`{{ url('items/get-by-customer') }}/${customerId}`)
            .then(response => response.json())
            .then(items => {
                itemsBody.querySelectorAll('.item-select').forEach(select => {
                    // Destroy Select2 before updating options
                    if ($(select).data('select2')) {
                        $(select).select2('destroy');
                    }
                    
                    select.innerHTML = '<option value="">Select Item</option>';
                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        option.dataset.price = item.price;
                        select.appendChild(option);
                    });
                    
                    // Reinitialize Select2 after updating options
                    initSelect2(select);
                });
            })
            .catch(error => console.error('Error loading items:', error));
    };

    const customerSelect = document.getElementById('customer_id');
    const customerAddressTextarea = document.getElementById('customer_address');

    customerSelect.addEventListener('change', (event) => {
        const selectedOption = event.target.selectedOptions[0];
        const address = selectedOption ? selectedOption.dataset.address : '';
        customerAddressTextarea.value = address;

        // Reset items to a single row when customer changes
        const rows = itemsBody.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            if (index > 0) row.remove();
        });

        const firstRow = itemsBody.querySelector('.item-row');
        firstRow.querySelectorAll('input').forEach(input => {
            if (input.classList.contains('qty-input')) input.value = '';
            else if (input.classList.contains('rate-input')) input.value = '';
            else if (input.classList.contains('remarks-input')) input.value = '';
            else if (input.classList.contains('delivery-date-input')) input.value = '';
        });
        const firstRowTotal = firstRow.querySelector('.total-input');
        if (firstRowTotal) firstRowTotal.value = formatCurrency(0);

        // Load items for selected customer
        loadItemsForCustomer(event.target.value);
        updateTotals();
        refreshRemoveButtons();
    });

    itemsBody.querySelector('.total-input').value = formatCurrency(0);
    refreshRemoveButtons();
    updateTotals();
    initDatePicker(itemsBody.querySelector('.delivery-date-input'));
    initSelect2(itemsBody.querySelector('.item-select'));
})();
</script>
@endpush
@endsection
