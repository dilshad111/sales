@extends('layouts.app')

@section('title', 'Edit Purchase Delivery Challan')

@section('content')
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white py-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-primary fw-bold font-outfit"><i class="fas fa-edit me-2"></i>Edit Purchase Delivery Challan</h4>
            <span class="badge bg-label-warning px-3 py-2 text-warning">Editing #{{ $purchase_delivery_challan->challan_number }}</span>
        </div>
    </div>
    <div class="card-body p-4">
        <form id="pdcForm" method="POST" action="{{ route('purchase_delivery_challans.update', $purchase_delivery_challan) }}">
            @csrf
            @method('PUT')

            <!-- Header Section -->
            <div class="row g-2 mb-4">
                <div class="col-md-3">
                    <label for="challan_number" class="form-label mb-1">SYSTEM DC#</label>
                    <input type="text" class="form-control form-control-sm bg-light" id="challan_number" value="{{ $purchase_delivery_challan->challan_number }}" readonly>
                </div>
                <div class="col-md-3">
                    <label for="supplier_dc_number" class="form-label mb-1">SUPPLIER DC#</label>
                    <input type="text" class="form-control form-control-sm" id="supplier_dc_number" name="supplier_dc_number" value="{{ $purchase_delivery_challan->supplier_dc_number }}" placeholder="Enter Reference #">
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label mb-1">DATE</label>
                    <input type="date" class="form-control form-control-sm" id="date" name="date" required value="{{ $purchase_delivery_challan->date->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="supplier_id" class="form-label mb-1">SUPPLIER</label>
                    <select class="form-select form-select-sm select2" id="supplier_id" name="supplier_id" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" data-address="{{ $supplier->address }}" {{ $purchase_delivery_challan->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-md-2">
                    <label for="vehicle_number" class="form-label mb-1">VEHICLE #</label>
                    <input type="text" class="form-control form-control-sm" id="vehicle_number" name="vehicle_number" value="{{ $purchase_delivery_challan->vehicle_number }}" placeholder="LHR-1234">
                </div>
                <div class="col-md-4">
                    <label for="supplier_address" class="form-label mb-1">SUPPLIER ADDRESS</label>
                    <textarea class="form-control form-control-sm bg-light" id="supplier_address" rows="1" readonly>{{ $purchase_delivery_challan->supplier->address }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="remarks" class="form-label mb-1">REMARKS</label>
                    <textarea class="form-control form-control-sm" id="remarks" name="remarks" rows="1" placeholder="Optional notes...">{{ $purchase_delivery_challan->remarks }}</textarea>
                </div>
            </div>

            <!-- Items Section -->
            <div class="mb-4">
                <h6 class="mb-3 text-secondary text-uppercase fw-bold border-bottom pb-2" style="font-size: 0.8rem; letter-spacing: 1px;">Received Items Details</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-sm custom-pdc-table" id="itemsTable">
                        <thead class="bg-light">
                            <tr class="text-center text-muted fw-bold" style="font-size: 0.65rem; background-color: #f8f9fa;">
                                <th class="col-sn">S.NO.</th>
                                <th class="col-item">ITEM NAME</th>
                                <th class="col-qty">QTY.</th>
                                <th class="col-uom">UOM</th>
                                <th class="col-rate">RATE</th>
                                <th class="col-amt">AMOUNT</th>
                                <th class="col-tax-p">TAX %</th>
                                <th class="col-tax-a">TAX AMT</th>
                                <th class="col-total">TOTAL</th>
                                <th class="col-action"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($purchase_delivery_challan->items as $index => $dcItem)
                            <tr class="item-row">
                                <td class="text-center sn-col small fw-bold text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <select class="form-select form-select-sm select2 item-select" name="items[{{ $index }}][purchase_item_id]" required>
                                        <option value="">Select Item</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-rate="{{ $item->purchase_price }}" data-unit="{{ $item->unit }}" {{ $dcItem->purchase_item_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm text-center qty-input" name="items[{{ $index }}][quantity]" value="{{ $dcItem->quantity }}" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-center bg-light unit-input" name="items[{{ $index }}][unit]" value="{{ $dcItem->unit }}" readonly>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm text-end rate-input" name="items[{{ $index }}][rate]" value="{{ $dcItem->rate }}" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end bg-light amount-input" readonly value="{{ number_format($dcItem->amount, 2) }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm text-center tax-percent-input" name="items[{{ $index }}][tax_percent]" value="{{ $dcItem->tax_percent }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end bg-light tax-amount-input" readonly value="{{ number_format($dcItem->tax_amount, 2) }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end bg-light total-amount-input fw-bold" readonly value="{{ number_format($dcItem->total_amount, 2) }}" style="color: #0d6efd;">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link text-danger p-0 remove-row" {{ $purchase_delivery_challan->items->count() == 1 ? 'disabled' : '' }} title="Delete Row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light fw-bold border-top-2">
                            <tr>
                                <td colspan="10" class="text-end py-2 px-4">
                                    <span class="text-muted fw-bold me-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">GRAND TOTAL:</span>
                                    <span class="text-primary fs-5" id="grand-total">{{ number_format($purchase_delivery_challan->total_amount, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" id="addRow" class="btn btn-outline-primary btn-sm mt-3 fw-bold shadow-sm" style="font-size: 0.7rem;">
                    <i class="fas fa-plus me-1"></i> ADD MORE ITEMS
                </button>
            </div>

            <div class="mt-5 border-top pt-4 text-end">
                <a href="{{ route('purchase_delivery_challans.index') }}" class="btn btn-outline-secondary px-4 me-2 fw-bold btn-sm">CANCEL</a>
                <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm btn-sm"><i class="fas fa-save me-2"></i>UPDATE PURCHASE DC</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* COLUMN WIDTH FLEXIBILITY */
    :root {
        --col-sn-width: 40px;
        --col-item-width: auto;
        --col-qty-width: 75px;
        --col-uom-width: 60px;
        --col-rate-width: 95px;
        --col-amt-width: 110px;
        --col-tax-p-width: 65px;
        --col-tax-a-width: 100px;
        --col-total-width: 120px;
        --col-action-width: 40px;
    }

    .col-sn { width: var(--col-sn-width); }
    .col-item { min-width: 250px; width: var(--col-item-width); }
    .col-qty { width: var(--col-qty-width); }
    .col-uom { width: var(--col-uom-width); }
    .col-rate { width: var(--col-rate-width); }
    .col-amt { width: var(--col-amt-width); }
    .col-tax-p { width: var(--col-tax-p-width); }
    .col-tax-a { width: var(--col-tax-a-width); }
    .col-total { width: var(--col-total-width); }
    .col-action { width: var(--col-action-width); }

    .font-outfit { font-family: 'Outfit', sans-serif; }
    .custom-pdc-table { table-layout: fixed; width: 100%; }
    .custom-pdc-table input, .custom-pdc-table select, .custom-pdc-table .select2-container--bootstrap-5 .select2-selection {
        font-size: 0.72rem !important; padding: 0.2rem 0.4rem !important; min-height: 28px !important; border-radius: 4px; width: 100%;
    }
    .custom-pdc-table .select2-container--bootstrap-5 .select2-selection--single { height: 28px !important; }
    .custom-pdc-table .select2-container--bootstrap-5 .select2-selection__rendered { line-height: 20px !important; }
    .custom-pdc-table th { padding: 6px 4px !important; vertical-align: middle !important; background-color: #f8f9fa !important; border-bottom: 2px solid #dee2e6 !important; white-space: nowrap; overflow: hidden; }
    .custom-pdc-table td { padding: 3px !important; overflow: hidden; }
    .form-label { font-size: 0.65rem !important; font-weight: 700; color: #6c757d; letter-spacing: 0.5px; text-transform: uppercase; }
    .select2-results__option { font-size: 0.75rem; }
    .bg-label-warning { background-color: #fff4e2 !important; color: #ffab00 !important; }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    function initSelect2(element) {
        element.select2({ theme: 'bootstrap-5', width: '100%' });
    }
    initSelect2($('.select2'));

    let itemIndex = {{ $purchase_delivery_challan->items->count() }};

    function formatNumber(num) {
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Filter Items by Supplier
    $('#supplier_id').on('change', function() {
        let supplierId = $(this).val();
        let address = $(this).find(':selected').data('address');
        $('#supplier_address').val(address || '');

        if (supplierId) {
            $.ajax({
                url: '{{ route("purchase_items.by_supplier", ":id") }}'.replace(':id', supplierId),
                type: 'GET',
                success: function(items) {
                    let options = '<option value="">Select Item</option>';
                    items.forEach(function(item) {
                        options += `<option value="${item.id}" data-rate="${item.purchase_price}" data-unit="${item.unit}">${item.name}</option>`;
                    });
                    
                    $('.item-select').each(function() {
                        $(this).html(options).val('').trigger('change.select2');
                        let row = $(this).closest('tr');
                        row.find('input').val('');
                        row.find('.tax-percent-input').val(0);
                    });
                    calculateTotals();
                }
            });
        } else {
            $('.item-select').html('<option value="">Select Item</option>').val('').trigger('change.select2');
        }
    });

    // Add Row
    $('#addRow').on('click', function() {
        let firstRow = $('.item-row:first');
        let newRow = firstRow.clone();
        
        newRow.find('.sn-col').text($('.item-row').length + 1);
        newRow.find('select, input').each(function() {
            let name = $(this).attr('name');
            if (name) {
                // Fix index replacement to handle existing indices
                $(this).attr('name', `items[${itemIndex}][${name.split('[')[2]}`);
            }
            if ($(this).is('input')) {
                if ($(this).hasClass('tax-percent-input')) {
                    $(this).val(0);
                } else {
                    $(this).val('');
                    if ($(this).hasClass('amount-input') || $(this).hasClass('tax-amount-input') || $(this).hasClass('total-amount-input')) {
                        $(this).val('0.00');
                    }
                }
            }
            if ($(this).is('select')) {
                $(this).val('');
            }
        });

        newRow.find('.select2-container').remove();
        $('#itemsBody').append(newRow);
        initSelect2(newRow.find('.select2'));
        
        itemIndex++;
        updateSerialNumbers();
    });

    // Remove Row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        updateSerialNumbers();
        calculateTotals();
    });

    function updateSerialNumbers() {
        $('.item-row').each(function(index) {
            $(this).find('.sn-col').text(index + 1);
        });
        $('.remove-row').prop('disabled', $('.item-row').length === 1);
    }

    // Auto-fetch Item Details
    $(document).on('change', '.item-select', function() {
        let row = $(this).closest('tr');
        let option = $(this).find(':selected');
        row.find('.rate-input').val(option.data('rate') || 0);
        row.find('.unit-input').val(option.data('unit') || '');
        calculateRow(row);
    });

    $(document).on('input', '.qty-input, .rate-input, .tax-percent-input', function() {
        calculateRow($(this).closest('tr'));
    });

    function calculateRow(row) {
        let qty = parseFloat(row.find('.qty-input').val()) || 0;
        let rate = parseFloat(row.find('.rate-input').val()) || 0;
        let taxPercent = parseFloat(row.find('.tax-percent-input').val()) || 0;

        let amount = qty * rate;
        let taxAmount = (amount * taxPercent) / 100;
        let totalAmount = amount + taxAmount;

        row.find('.amount-input').val(formatNumber(amount));
        row.find('.tax-amount-input').val(formatNumber(taxAmount));
        row.find('.total-amount-input').val(formatNumber(totalAmount));

        calculateTotals();
    }

    function calculateTotals() {
        let grandTotal = 0;
        $('.total-amount-input').each(function() {
            let val = $(this).val().replace(/,/g, '');
            grandTotal += parseFloat(val) || 0;
        });
        $('#grand-total').text(formatNumber(grandTotal));
    }
});
</script>
@endpush
@endsection
