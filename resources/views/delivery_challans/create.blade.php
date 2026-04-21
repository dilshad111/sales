@extends('layouts.app')

@section('title', 'Create Delivery Challan')

@section('content')
<h1><i class="fas fa-plus-circle me-2"></i>Create Delivery Challan</h1>
<link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" />
<form id="challanForm" method="POST" action="{{ route('delivery_challans.store') }}">
    @csrf

    <!-- Challan Number and Date Row -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label for="challan_number" class="form-label"><i class="fas fa-hashtag me-1"></i>Challan Number</label>
            <input type="text" class="form-control" id="challan_number" value="{{ $nextChallanNumber }}" disabled>
            <small class="form-text text-muted">Auto-generated challan number</small>
        </div>
        <div class="col-md-4">
            <label for="challan_date" class="form-label"><i class="fas fa-calendar me-1"></i>Challan Date</label>
            <input type="date" class="form-control" id="challan_date" name="challan_date" required value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
        </div>
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
        <div class="col-md-5">
            <label for="customer_address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Customer Address</label>
            <textarea class="form-control" id="customer_address" rows="2" readonly placeholder="Select a customer to view address"></textarea>
        </div>
        <div class="col-md-4">
            <label for="remarks" class="form-label"><i class="fas fa-comment me-1"></i>Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Optional remarks for this challan"></textarea>
        </div>
        <div class="col-md-3">
            <label for="vehicle_number" class="form-label"><i class="fas fa-truck me-1"></i>Vehicle Number</label>
            <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="Enter vehicle details">
        </div>
    </div>

    <!-- Items Section -->
    <div class="row g-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-boxes me-2"></i>Challan Items</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-sm" id="itemsTable">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="min-width:280px;">Item</th>
                            <th style="width:100px;">Qty.</th>
                            <th style="width:250px;">Bundles</th>
                            <th style="width:150px; display:none;">Rate</th>
                            <th style="width:150px; display:none;">Total</th>
                            <th style="width:250px;">Remarks</th>
                            <th style="width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr class="item-row">
                            <td style="min-width:300px;">
                                <select class="form-select form-select-sm item-select" name="items[0][item_id]" required>
                                    <option value="">Select Item</option>
                                </select>
                            </td>
                            <td style="width:100px;">
                                <input type="text" class="form-control form-control-sm text-center qty-input" name="items[0][quantity]" placeholder="0" required>
                            </td>
                            <td style="width:250px;">
                                <input type="text" class="form-control form-control-sm bundles-input" name="items[0][bundles]" placeholder="Bundles Details">
                            </td>
                            <td style="display:none;">
                                <input type="text" class="form-control form-control-sm text-end rate-input" name="items[0][rate]" value="0">
                            </td>
                            <td style="display:none;">
                                <input type="text" class="form-control form-control-sm text-end total-input" value="0.00" readonly>
                            </td>
                            <td style="width:250px;">
                                <input type="text" class="form-control form-control-sm remarks-input" name="items[0][remarks]" placeholder="Line Remarks">
                            </td>
                            <td class="text-center" style="width:80px;">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item" disabled><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td class="text-end fw-bold">Total Qty:</td>
                            <td class="text-center fw-bold text-primary" id="totalQtyDisplay">0</td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" id="addItem" class="btn btn-secondary btn-sm mt-2"><i class="fas fa-plus me-1"></i>Add Item</button>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Create Challan</button>
        <a href="{{ route('delivery_challans.index') }}" class="btn btn-secondary ms-2"><i class="fas fa-times me-1"></i>Cancel</a>
    </div>
</form>

@push('scripts')
<!-- Local script for Flatpickr -->
<script src="{{ asset('js/flatpickr.min.js') }}"></script>
<script>
(() => {
    const parseNumber = (value) => {
        if (value === undefined || value === null) return 0;
        const numeric = value.toString().replace(/,/g, '').trim();
        const parsed = parseFloat(numeric);
        return Number.isNaN(parsed) ? 0 : parsed;
    };

    const itemsBody = document.getElementById('itemsBody');
    const addItemBtn = document.getElementById('addItem');
    let itemIndex = itemsBody.querySelectorAll('.item-row').length;

    const updateTotalQty = () => {
        let total = 0;
        document.querySelectorAll('.qty-input').forEach(input => {
            const val = parseNumber(input.value);
            total += isNaN(val) ? 0 : val;
        });
        const display = document.getElementById('totalQtyDisplay');
        if (display) display.textContent = total.toLocaleString();
    };

    const refreshRemoveButtons = () => {
        const rows = itemsBody.querySelectorAll('.item-row');
        rows.forEach((row) => {
            const removeBtn = row.querySelector('.remove-item');
            removeBtn.disabled = rows.length === 1;
        });
    };

    const initSelect2 = (select) => {
        if (!select) return;
        if ($(select).data('select2')) $(select).select2('destroy');
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
        const firstSelect = firstRow.querySelector('.item-select');
        if ($(firstSelect).data('select2')) $(firstSelect).select2('destroy');
        const template = firstRow.cloneNode(true);
        initSelect2(firstSelect);

        template.querySelectorAll('input, select').forEach((input) => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            }
            if (input.classList.contains('item-select')) input.selectedIndex = 0;
            else if (input.classList.contains('qty-input')) input.value = '';
            else if (input.classList.contains('bundles-input')) input.value = '';
            else if (input.classList.contains('rate-input')) input.value = '0';
            else if (input.classList.contains('remarks-input')) input.value = '';
        });

        template.querySelector('.remove-item').disabled = false;
        itemsBody.appendChild(template);
        initSelect2(template.querySelector('.item-select'));
        itemIndex++;
        refreshRemoveButtons();
    };

    itemsBody.addEventListener('click', (event) => {
        const btn = event.target.closest('.remove-item');
        if (btn) {
            event.preventDefault();
            const rows = itemsBody.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('.item-row').remove();
                refreshRemoveButtons();
                updateTotalQty();
            }
        }
    });

    itemsBody.addEventListener('input', (e) => {
        if (e.target.classList.contains('qty-input')) {
            updateTotalQty();
        }
    });

    addItemBtn.addEventListener('click', addItemRow);

    const loadItemsForCustomer = (customerId) => {
        if (!customerId) return;
        fetch(`{{ url('items/get-by-customer') }}/${customerId}`)
            .then(response => response.json())
            .then(items => {
                itemsBody.querySelectorAll('.item-select').forEach(select => {
                    if ($(select).data('select2')) $(select).select2('destroy');
                    select.innerHTML = '<option value="">Select Item</option>';
                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        option.dataset.price = item.price || 0;
                        select.appendChild(option);
                    });
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
        
        // Reset items to a single row when customer changes to avoid confusion or duplication
        const rows = itemsBody.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            if (index > 0) row.remove();
        });
        
        const firstRow = itemsBody.querySelector('.item-row');
        firstRow.querySelectorAll('input').forEach(input => {
            if (input.classList.contains('qty-input')) input.value = '';
            else if (input.classList.contains('rate-input')) input.value = '0';
            else if (input.classList.contains('bundles-input')) input.value = '';
            else if (input.classList.contains('remarks-input')) input.value = '';
        });

        loadItemsForCustomer(event.target.value);
        updateTotalQty();
        refreshRemoveButtons();
    });

    itemsBody.addEventListener('change', (e) => {
        if (e.target.classList.contains('item-select')) {
            const row = e.target.closest('.item-row');
            const selectedOption = e.target.selectedOptions[0];
            const rateInput = row.querySelector('.rate-input');
            const price = selectedOption ? (selectedOption.dataset.price || 0) : 0;
            if (rateInput) {
                rateInput.value = price;
            }
        }
    });

    refreshRemoveButtons();
    initSelect2(itemsBody.querySelector('.item-select'));
    updateTotalQty();
})();
</script>
@endpush
@endsection
