@extends('layouts.app')

@section('title', 'Edit Delivery Challan')

@section('content')
<h1><i class="fas fa-edit me-2"></i>Edit Delivery Challan — {{ $deliveryChallan->challan_number }}</h1>
<link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" />
<form id="challanForm" method="POST" action="{{ route('delivery_challans.update', $deliveryChallan) }}">
    @csrf
    @method('PUT')

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label for="challan_number" class="form-label"><i class="fas fa-hashtag me-1"></i>Challan Number</label>
            <input type="text" class="form-control" id="challan_number" value="{{ $deliveryChallan->challan_number }}" disabled>
        </div>
        <div class="col-md-4">
            <label for="challan_date" class="form-label"><i class="fas fa-calendar me-1"></i>Challan Date</label>
            <input type="date" class="form-control" id="challan_date" name="challan_date" required value="{{ $deliveryChallan->challan_date->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-4">
            <label for="customer_id" class="form-label"><i class="fas fa-users me-1"></i>Customer</label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" data-address="{{ $customer->address }}" {{ $deliveryChallan->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <label for="customer_address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Customer Address</label>
            <textarea class="form-control" id="customer_address" rows="2" readonly>{{ optional($deliveryChallan->customer)->address }}</textarea>
        </div>
        <div class="col-md-4">
            <label for="remarks" class="form-label"><i class="fas fa-comment me-1"></i>Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Optional remarks">{{ $deliveryChallan->remarks }}</textarea>
        </div>
        <div class="col-md-3">
            <label for="vehicle_number" class="form-label"><i class="fas fa-truck me-1"></i>Vehicle Number</label>
            <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" value="{{ $deliveryChallan->vehicle_number }}" placeholder="Enter vehicle details">
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-boxes me-2"></i>Challan Items</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-sm" id="itemsTable">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="min-width:280px;">Item</th>
                            <th style="width:150px;">Qty.</th>
                            <th style="width:250px;">Bundles</th>
                            <th style="width:150px; display:none;">Rate</th>
                            <th style="width:150px; display:none;">Total</th>
                            <th style="width:150px;">Remarks</th>
                            <th style="width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach($deliveryChallan->items as $index => $challanItem)
                        <tr class="item-row">
                            <td style="min-width:300px;">
                                <select class="form-select form-select-sm item-select" name="items[{{ $index }}][item_id]" required>
                                    <option value="">Select Item</option>
                                    @foreach($itemsForCustomer as $item)
                                        <option value="{{ $item->id }}" {{ $challanItem->item_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width:100px;">
                                <input type="text" class="form-control form-control-sm text-center qty-input" name="items[{{ $index }}][quantity]" value="{{ number_format($challanItem->quantity) }}" required>
                            </td>
                            <td style="width:250px;">
                                <input type="text" class="form-control form-control-sm bundles-input" name="items[{{ $index }}][bundles]" value="{{ $challanItem->bundles }}" placeholder="Bundles Details">
                            </td>
                            <td style="display:none;">
                                <input type="text" class="form-control form-control-sm text-end rate-input" name="items[{{ $index }}][rate]" value="{{ number_format($challanItem->price, 2) }}">
                            </td>
                            <td style="display:none;">
                                <input type="text" class="form-control form-control-sm text-end total-input" value="{{ number_format($challanItem->total, 2) }}" readonly>
                            </td>
                            <td style="width:250px;">
                                <input type="text" class="form-control form-control-sm remarks-input" name="items[{{ $index }}][remarks]" value="{{ $challanItem->remarks }}" placeholder="Line Remarks">
                            </td>
                            <td class="text-center" style="width:80px;">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
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
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update Challan</button>
        <a href="{{ route('delivery_challans.show', $deliveryChallan) }}" class="btn btn-secondary ms-2"><i class="fas fa-times me-1"></i>Cancel</a>
    </div>
</form>

@push('scripts')
<!-- Local script for Flatpickr -->
<script src="{{ asset('js/flatpickr.min.js') }}"></script>
<script>
(() => {
    const itemsBody = document.getElementById('itemsBody');
    const addItemBtn = document.getElementById('addItem');
    let itemIndex = itemsBody.querySelectorAll('.item-row').length;

    const parseNumber = (value) => {
        if (value === undefined || value === null) return 0;
        const numeric = value.toString().replace(/,/g, '').trim();
        const parsed = parseFloat(numeric);
        return Number.isNaN(parsed) ? 0 : parsed;
    };

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
            row.querySelector('.remove-item').disabled = rows.length === 1;
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
            if (input.name) input.name = input.name.replace(/\[\d+\]/, `[${itemIndex}]`);
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

    itemsBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-item');
        if (btn) {
            e.preventDefault();
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
            .then(r => r.json())
            .then(items => {
                itemsBody.querySelectorAll('.item-select').forEach(select => {
                    if ($(select).data('select2')) $(select).select2('destroy');
                    const selected = select.value;
                    select.innerHTML = '<option value="">Select Item</option>';
                    items.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        opt.dataset.price = item.price || 0;
                        if (item.id == selected) opt.selected = true;
                        select.appendChild(opt);
                    });
                    initSelect2(select);
                });
            });
    };

    const customerSelect = document.getElementById('customer_id');
    const customerAddressTextarea = document.getElementById('customer_address');

    customerSelect.addEventListener('change', (e) => {
        const selectedOption = e.target.selectedOptions[0];
        customerAddressTextarea.value = selectedOption ? selectedOption.dataset.address : '';
        loadItemsForCustomer(e.target.value);
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

    itemsBody.querySelectorAll('.item-row').forEach((row) => {
        initSelect2(row.querySelector('.item-select'));
    });

    refreshRemoveButtons();
    updateTotalQty();
})();
</script>
@endpush
@endsection
