@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
<h1><i class="fas fa-plus-circle me-2"></i>Create Bill</h1>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<form id="billForm" method="POST" action="{{ route('bills.store') }}">
    @csrf
    <style>
        #itemsTable .form-control, 
        #itemsTable .form-select {
            padding: 1rem 1.2rem;
            font-size: 1.25rem;
            height: auto;
            border-radius: 0.85rem;
            font-weight: 500;
        }
        #itemsTable .select2-container--bootstrap-5 .select2-selection {
            min-height: 60px;
            padding-top: 12px;
            border-radius: 0.85rem;
        }
        #itemsTable .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            font-size: 1.25rem;
            font-weight: 500;
        }
        .table > :not(caption) > * > * {
            padding: 1.25rem 0.5rem;
        }
        #itemsTable thead th {
            text-align: center !important;
            vertical-align: middle;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }
    </style>

    <!-- Bill Number and Date Row -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="bill_number" class="form-label"><i class="fas fa-hashtag me-1"></i>Bill Number</label>
            <input type="text" class="form-control" id="bill_number" value="{{ $nextBillNumber }}" disabled>
            <small class="form-text text-muted">Auto-generated bill number</small>
        </div>
        <div class="col-md-6">
            <label for="bill_date" class="form-label"><i class="fas fa-calendar me-1"></i>Bill Date</label>
            <input type="date" class="form-control" id="bill_date" name="bill_date" required value="{{ date('Y-m-d') }}">
        </div>
    </div>

    <!-- Customer Dropdown Row -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label for="customer_id" class="form-label"><i class="fas fa-users me-1"></i>Customer</label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Customer Address Row -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <label for="customer_address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Customer Address</label>
            <textarea class="form-control" id="customer_address" name="customer_address" rows="3" readonly placeholder="Select a customer to view address"></textarea>
        </div>
    </div>

    <!-- Items Section -->
    <div class="row g-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Bill Items</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable" style="min-width: 1600px;">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 550px;">Item</th>
                            <th style="width: 220px;">Qty</th>
                            <th style="width: 250px;">Rate</th>
                            <th style="width: 280px;">Total Amount</th>
                            <th style="width: 320px;">Delivery Date</th>
                            <th style="width: 450px;">Remarks</th>
                            <th style="width: 150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td style="min-width: 400px;">
                                <select class="form-select item-select" name="items[0][item_id]" required>
                                    <option value="">Select Item</option>
                                </select>
                            </td>
                            <td style="width: 220px;">
                                <input type="text" class="form-control text-end qty-input" name="items[0][quantity]" placeholder="0" required>
                            </td>
                            <td style="width: 250px;">
                                <input type="text" class="form-control text-end rate-input" name="items[0][rate]" placeholder="0.00" required>
                            </td>
                            <td style="width: 280px;">
                                <input type="text" class="form-control text-end total-input" value="0.00" readonly>
                            </td>
                            <td style="width: 320px;">
                                <input type="text" class="form-control delivery-date-input" name="items[0][delivery_date]" placeholder="dd/mm/yyyy" autocomplete="off">
                            </td>
                            <td style="width: 450px;">
                                <input type="text" class="form-control remarks-input" name="items[0][remarks]" placeholder="Remarks">
                            </td>
                            <td class="text-center" style="width: 150px;">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item" disabled>Remove</button>
                            </td>
                        </tr>
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
                                <td class="text-end"><span id="subTotal">0.00</span></td>
                            </tr>
                            <tr>
                                <th>Discount</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end currency-input" id="discount" name="discount" value="0.00">
                                </td>
                            </tr>
                            <tr>
                                <th>Tax</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end currency-input" id="tax" name="tax" value="0.00">
                                </td>
                            </tr>
                            <tr class="table-light">
                                <th class="fw-semibold">Grand Total</th>
                                <td class="text-end fw-semibold"><span id="grandTotal">0.00</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Create Bill</button>
        <a href="{{ route('bills.index') }}" class="btn btn-secondary ms-2"><i class="fas fa-times me-1"></i>Cancel</a>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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

        // Load items for selected customer
        loadItemsForCustomer(event.target.value);
    });

    itemsBody.querySelector('.total-input').value = formatCurrency(0);
    refreshRemoveButtons();
    updateTotals();
    initDatePicker(itemsBody.querySelector('.delivery-date-input'));
    initSelect2(itemsBody.querySelector('.item-select'));
})();
</script>
@endsection
