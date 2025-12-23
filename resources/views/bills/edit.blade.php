@extends('layouts.app')

@section('title', 'Edit Bill')

@section('content')
<h1><i class="fas fa-edit me-2"></i>Edit Bill</h1>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<form id="billForm" method="POST" action="{{ route('bills.update', $bill) }}">
    @csrf
    @method('PUT')

    <!-- Bill Number and Date Row -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="bill_number" class="form-label"><i class="fas fa-hashtag me-1"></i>Bill Number</label>
            <input type="text" class="form-control" id="bill_number" value="{{ $bill->bill_number }}" disabled>
            <small class="form-text text-muted">Bill number cannot be changed</small>
        </div>
        <div class="col-md-6">
            <label for="bill_date" class="form-label"><i class="fas fa-calendar me-1"></i>Bill Date</label>
            <input type="date" class="form-control" id="bill_date" name="bill_date" required value="{{ $bill->bill_date->format('Y-m-d') }}">
        </div>
    </div>

    <!-- Customer Dropdown Row -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label for="customer_id" class="form-label"><i class="fas fa-users me-1"></i>Customer</label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" data-address="{{ $customer->address }}" {{ $bill->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Customer Address Row -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <label for="customer_address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Customer Address</label>
            <textarea class="form-control" id="customer_address" name="customer_address" rows="3" readonly>{{ $bill->customer ? $bill->customer->address : '' }}</textarea>
        </div>
    </div>

    <!-- Items Section -->
    <div class="row g-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Bill Items</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th class="text-end" style="width: 110px;">Qty</th>
                            <th class="text-end" style="width: 140px;">Rate</th>
                            <th class="text-end" style="width: 160px;">Total Amount</th>
                            <th style="width: 160px;">Delivery Date</th>
                            <th style="width: 160px;">Remarks</th>
                            <th class="text-center" style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach($bill->billItems as $index => $billItem)
                        <tr class="item-row">
                            <td>
                                <select class="form-select item-select" name="items[{{ $index }}][item_id]" required>
                                    <option value="">Select Item</option>
                                    @foreach($itemsForCustomer as $item)
                                        <option value="{{ $item->id }}" data-price="{{ $item->price }}" {{ $billItem->item_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control text-end qty-input" name="items[{{ $index }}][quantity]" value="{{ $billItem->quantity }}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control text-end rate-input" name="items[{{ $index }}][rate]" value="{{ number_format($billItem->price, 2) }}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control text-end total-input" value="{{ number_format($billItem->total, 2) }}" readonly>
                            </td>
                            <td>
                                <input type="date" class="form-control delivery-date-input" name="items[{{ $index }}][delivery_date]" value="{{ $billItem->delivery_date ? $billItem->delivery_date->format('Y-m-d') : '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control remarks-input" name="items[{{ $index }}][remarks]" value="{{ $billItem->remarks }}" placeholder="Remarks">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item" {{ $bill->billItems->count() == 1 ? 'disabled' : '' }}>Remove</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" id="addItem" class="btn btn-secondary btn-sm mt-2"><i class="fas fa-plus me-1"></i>Add Item</button>

            <div class="row justify-content-end mt-4">
                <div class="col-md-6 col-lg-5">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr>
                                <th>Sub Total</th>
                                <td class="text-end"><span id="subTotal">{{ number_format($bill->billItems->sum('total'), 2) }}</span></td>
                            </tr>
                            <tr>
                                <th>Discount</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end currency-input" id="discount" name="discount" value="{{ number_format($bill->discount, 2) }}">
                                </td>
                            </tr>
                            <tr>
                                <th>Tax</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end currency-input" id="tax" name="tax" value="{{ number_format($bill->tax, 2) }}">
                                </td>
                            </tr>
                            <tr class="table-light">
                                <th class="fw-semibold">Grand Total</th>
                                <td class="text-end fw-semibold"><span id="grandTotal">{{ number_format($bill->total, 2) }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update Bill</button>
        <a href="{{ route('bills.show', $bill) }}" class="btn btn-info ms-2"><i class="fas fa-eye me-1"></i>View Bill</a>
        <a href="{{ route('bills.index') }}" class="btn btn-secondary ms-2"><i class="fas fa-times me-1"></i>Cancel</a>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
    const taxInput = document.getElementById('tax');
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

    const updateTotals = () => {
        let subtotal = 0;
        itemsBody.querySelectorAll('.total-input').forEach((input) => {
            subtotal += parseNumber(input.value);
        });

        const discount = parseNumber(discountInput.value);
        const tax = parseNumber(taxInput.value);

        subTotalEl.textContent = formatCurrency(subtotal);
        grandTotalEl.textContent = formatCurrency(subtotal - discount + tax);
    };

    const handleItemChange = (select) => {
        const row = select.closest('.item-row');
        const rateInput = row.querySelector('.rate-input');
        const price = parseNumber(select.selectedOptions[0]?.dataset.price || 0);

        rateInput.value = price ? formatCurrency(price) : '';
        updateRowTotal(row);
        updateTotals();
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
        $(firstSelect).select2({
            theme: 'bootstrap-5',
            placeholder: 'Select Item',
            allowClear: true,
            width: '100%'
        });
        
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
        
        // Initialize Select2 on the new row
        $(template.querySelector('.item-select')).select2({
            theme: 'bootstrap-5',
            placeholder: 'Select Item',
            allowClear: true,
            width: '100%'
        });

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

    [discountInput, taxInput].forEach((input) => {
        input.addEventListener('input', updateTotals);
        input.addEventListener('focusout', () => {
            input.value = formatCurrency(parseNumber(input.value));
            updateTotals();
        });
        input.value = formatCurrency(parseNumber(input.value));
    });

    const loadItemsForCustomer = (customerId) => {
        if (!customerId) return;

        fetch(`/items/get-by-customer/${customerId}`)
            .then(response => response.json())
            .then(items => {
                itemsBody.querySelectorAll('.item-select').forEach(select => {
                    select.innerHTML = '<option value="">Select Item</option>';
                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        option.dataset.price = item.price;
                        select.appendChild(option);
                    });
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

        // Load items for selected customer
        loadItemsForCustomer(event.target.value);
    });

    // Initialize Select2 for all item dropdowns
    $('.item-select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select Item',
        allowClear: true,
        width: '100%'
    });

    // Initialize totals
    updateTotals();
    refreshRemoveButtons();
})();
</script>
@endsection
