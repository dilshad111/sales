@extends('layouts.app')

@section('title', 'Create Delivery Challan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck me-2 text-primary"></i>Create Delivery Challan
            @if(isset($salesOrder))
                <small class="text-muted fs-6">from SO: {{ $salesOrder->so_number }}</small>
            @endif
        </h1>
        <a href="{{ route('delivery_challans.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form id="challanForm" method="POST" action="{{ route('delivery_challans.store') }}">
        @csrf
        
        @if(isset($salesOrder))
            <input type="hidden" name="sales_order_id" value="{{ $salesOrder->id }}">
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Challan Header</h6>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="challan_number" class="form-label">Challan Number</label>
                        <input type="text" class="form-control" id="challan_number" value="{{ $nextChallanNumber }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label for="challan_date" class="form-label">Challan Date</label>
                        <input type="date" class="form-control" id="challan_date" name="challan_date" required value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select class="form-select select2-customer" id="customer_id" name="customer_id" required {{ isset($salesOrder) ? 'disabled' : '' }}>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    data-address="{{ $customer->address }}" 
                                    data-excess-percent="{{ $customer->excess_qty_percent }}"
                                    {{ (isset($salesOrder) && $salesOrder->customer_id == $customer->id) ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(isset($salesOrder))
                            <input type="hidden" name="customer_id" value="{{ $salesOrder->customer_id }}">
                        @endif
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="customer_address" class="form-label">Customer Address</label>
                        <input type="text" class="form-control" id="customer_address" readonly tabindex="-1" value="{{ isset($salesOrder) ? $salesOrder->customer->address : '' }}">
                    </div>
                    <div class="col-md-2">
                        <label for="excess_percent" class="form-label">Excess Allowed %</label>
                        <input type="text" class="form-control bg-light" id="excess_percent" readonly tabindex="-1" value="{{ isset($salesOrder) ? (float)$salesOrder->customer->excess_qty_percent . '%' : '0%' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="vehicle_number" class="form-label">Vehicle Number</label>
                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="Enter vehicle details">
                    </div>
                    <div class="col-md-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Optional remarks">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Challan Items</h6>
                @if(!isset($salesOrder))
                <button type="button" id="addItem" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add Item
                </button>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="itemsTable">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">S.No.</th>
                                <th style="min-width: 250px;">Item</th>
                                @if(isset($salesOrder))
                                <th style="width: 120px;">Rem. Qty</th>
                                @endif
                                <th style="width: 150px;">Del. Qty</th>
                                <th style="width: 150px;">Bundles</th>
                                <th style="width: 150px;">Remarks</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @if(isset($salesOrder))
                                @foreach($salesOrder->items as $index => $soItem)
                                @php
                                    $remaining = $soItem->remaining_quantity;
                                @endphp
                                @if($remaining > 0 || (float)$salesOrder->customer->excess_qty_percent > 0)
                                <tr class="item-row">
                                    <td class="text-center align-middle row-sno">{{ $index + 1 }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" value="{{ $soItem->item->name }}" readonly>
                                        <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $soItem->item_id }}">
                                        <input type="hidden" name="items[{{ $index }}][sales_order_item_id]" value="{{ $soItem->id }}">
                                        <input type="hidden" class="original-qty" value="{{ $soItem->quantity }}">
                                        <input type="hidden" class="delivered-so-far" value="{{ $soItem->delivered_quantity }}">
                                    </td>
                                    <td class="text-center align-middle fw-bold text-info">
                                        {{ $remaining }}
                                        <small class="d-block text-muted" style="font-size: 0.7rem;">of {{ $soItem->quantity }}</small>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center qty-input" name="items[{{ $index }}][quantity]" value="{{ $remaining }}" min="1" required>
                                        <div class="invalid-feedback excess-warning" style="display: none;">Exceeds limit!</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="items[{{ $index }}][bundles]" placeholder="e.g. 10 Bags">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="items[{{ $index }}][remarks]" value="{{ $soItem->remarks }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-link text-danger remove-item p-0">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            @else
                                <tr class="item-row">
                                    <td class="text-center align-middle row-sno">1</td>
                                    <td>
                                        <select class="form-select form-select-sm item-select" name="items[0][item_id]" required>
                                            <option value="">Select Item</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center qty-input" name="items[0][quantity]" min="1" required>
                                        <div class="invalid-feedback excess-warning" style="display: none;">Exceeds limit!</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="items[0][bundles]" placeholder="e.g. 10 Bags">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="items[0][remarks]">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-link text-danger remove-item p-0" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="{{ isset($salesOrder) ? 3 : 2 }}" class="text-end">Total Delivered Quantity:</th>
                                <th id="totalQtyDisplay" class="text-center fw-bold text-primary">0</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="mb-5 text-end">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" id="submitBtn">
                <i class="fas fa-save me-2"></i> Save Delivery Challan
            </button>
        </div>
    </form>
</div>

@push('styles')
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        font-size: 0.875rem;
        min-height: 31px;
    }
    .excess-warning {
        font-size: 0.7rem;
        color: #dc3545;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
$(document).ready(function() {
    function initSelect2(element, placeholder = 'Select') {
        $(element).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholder,
            allowClear: true,
            closeOnSelect: true
        });
    }

    // Initialize customer select
    initSelect2('.select2-customer', 'Select Customer');
    
    // Initialize existing item selects
    $('.item-select').each(function() {
        initSelect2(this, 'Select Item');
    });

    let itemIndex = {{ isset($salesOrder) ? $salesOrder->items->count() : 1 }};

    function updateSNo() {
        $('.row-sno').each(function(index) {
            $(this).text(index + 1);
        });
    }

    function calculateTotalQty() {
        let total = 0;
        $('.qty-input').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        $('#totalQtyDisplay').text(total.toLocaleString());
    }

    function toggleRemoveButtons() {
        let rows = $('.item-row');
        if (rows.length <= 1) {
            rows.find('.remove-item').prop('disabled', true);
        } else {
            rows.find('.remove-item').prop('disabled', false);
        }
    }

    function checkExcessQty(row) {
        let qtyInput = row.find('.qty-input');
        let originalQty = parseFloat(row.find('.original-qty').val());
        let deliveredSoFar = parseFloat(row.find('.delivered-so-far').val()) || 0;
        
        if (isNaN(originalQty)) return true; // Not from SO

        let excessPercent = parseFloat($('#customer_id').find(':selected').data('excess-percent')) || 0;
        let currentDelivering = parseFloat(qtyInput.val()) || 0;
        
        let maxTotalAllowed = originalQty * (1 + (excessPercent / 100));
        let maxRemainingAllowed = maxTotalAllowed - deliveredSoFar;

        if (currentDelivering > maxRemainingAllowed) {
            qtyInput.addClass('is-invalid');
            row.find('.excess-warning').text('Max allowed: ' + Math.floor(maxRemainingAllowed)).show();
            return false;
        } else {
            qtyInput.removeClass('is-invalid');
            row.find('.excess-warning').hide();
            return true;
        }
    }

    $('#addItem').click(function() {
        let firstRow = $('.item-row:first');
        let newRow = firstRow.clone();
        
        newRow.find('input').val('');
        newRow.find('.qty-input').removeClass('is-invalid');
        newRow.find('.excess-warning').hide();
        newRow.find('.original-qty').remove(); 
        newRow.find('.delivered-so-far').remove();
        
        newRow.find('[name]').each(function() {
            let name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/\[\d+\]/, '[' + itemIndex + ']'));
            }
        });

        // Remove the Select2 container from the cloned row
        newRow.find('.select2-container').remove();
        // Show the original select and re-initialize it
        newRow.find('.item-select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').show();
        
        $('#itemsBody').append(newRow);
        initSelect2(newRow.find('.item-select'), 'Select Item');
        itemIndex++;
        updateSNo();
        toggleRemoveButtons();
        
        let customerId = $('#customer_id').val();
        if (customerId) {
            updateRowItems(newRow.find('.item-select'), customerId);
        }
    });

    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            updateSNo();
            calculateTotalQty();
            toggleRemoveButtons();
        } else {
            alert('At least one item is required.');
        }
    });

    $('#customer_id').on('change', function() {
        let selectedOption = $(this).find(':selected');
        let address = selectedOption.data('address');
        let excessPercent = selectedOption.data('excess-percent') || 0;
        $('#customer_address').val(address || '');
        $('#excess_percent').val(parseFloat(excessPercent) + '%');

        @if(!isset($salesOrder))
        let customerId = $(this).val();
        if (customerId) {
            updateAllItemsDropdowns(customerId);
        } else {
            $('.item-select').empty().append('<option value="">Select Item</option>').trigger('change');
        }
        @endif
        
        $('.item-row').each(function() {
            checkExcessQty($(this));
        });
    });

    function updateAllItemsDropdowns(customerId) {
        $.get('{{ url("items/get-by-customer") }}/' + customerId, function(items) {
            $('.item-select').each(function() {
                let select = $(this);
                let currentValue = select.val();
                select.empty().append('<option value="">Select Item</option>');
                items.forEach(function(item) {
                    select.append(`<option value="${item.id}" ${item.id == currentValue ? 'selected' : ''}>${item.name}</option>`);
                });
                select.trigger('change.select2');
            });
        });
    }

    function updateRowItems(select, customerId) {
        $.get('{{ url("items/get-by-customer") }}/' + customerId, function(items) {
            select.empty().append('<option value="">Select Item</option>');
            items.forEach(function(item) {
                select.append(`<option value="${item.id}">${item.name}</option>`);
            });
            select.trigger('change.select2');
        });
    }

    $(document).on('input', '.qty-input', function() {
        calculateTotalQty();
        checkExcessQty($(this).closest('.item-row'));
    });

    $('#challanForm').submit(function(e) {
        let isValid = true;
        $('.item-row').each(function() {
            if (!checkExcessQty($(this))) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Some items exceed the allowed quantity limit based on the Sales Order and Customer Allowance.');
        }
    });

    calculateTotalQty();
    toggleRemoveButtons();
    @if(isset($salesOrder))
        $('.item-row').each(function() {
            checkExcessQty($(this));
        });
    @endif
});
</script>
@endpush
@endsection
