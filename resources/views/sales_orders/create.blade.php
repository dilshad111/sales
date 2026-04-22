@extends('layouts.app')

@section('title', 'Create Sales Order')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice me-2"></i>Create Sales Order</h1>
                <a href="{{ route('sales_orders.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="salesOrderForm" method="POST" action="{{ route('sales_orders.store') }}">
                @csrf

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Sales Order Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label for="so_number" class="form-label">SO Number</label>
                                <input type="text" class="form-control" id="so_number" value="AUTO" disabled>
                                <small class="text-muted">Format: SO# 00001</small>
                            </div>
                            <div class="col-md-2">
                                <label for="so_date" class="form-label">SO Date</label>
                                <input type="date" class="form-control" id="so_date" name="so_date" required value="{{ $so_date }}">
                            </div>
                            <div class="col-md-2">
                                <label for="po_number" class="form-label">P.O. Number</label>
                                <input type="text" class="form-control" id="po_number" name="po_number" placeholder="Customer P.O. #">
                            </div>
                            <div class="col-md-2">
                                <label for="po_date" class="form-label">P.O. Date</label>
                                <input type="date" class="form-control" id="po_date" name="po_date">
                            </div>
                            <div class="col-md-3">
                                <label for="tax_percent" class="form-label">Sales Tax (%)</label>
                                <select class="form-select" id="tax_percent" name="tax_percent">
                                    <option value="0">0% (No Tax)</option>
                                    <option value="18">18% GST</option>
                                    <option value="17">17% GST</option>
                                    <option value="13">13% SRB</option>
                                    <option value="5">5% VAT</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-select select2" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_address" class="form-label">Customer Address</label>
                                <input type="text" class="form-control" id="customer_address" readonly tabindex="-1">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Internal or external remarks"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                        <button type="button" id="addItem" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="itemsTable">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 50px;">S.No.</th>
                                        <th style="min-width: 300px;">Item</th>
                                        <th style="width: 150px;">Quantity</th>
                                        <th style="width: 150px;">Unit Price</th>
                                        <th style="width: 150px;">Total</th>
                                        <th style="width: 160px;">Del. Date</th>
                                        <th style="width: 150px;">Remarks</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <tr class="item-row">
                                        <td class="text-center align-middle row-sno">1</td>
                                        <td>
                                            <select class="form-select form-select-sm item-select" name="items[0][item_id]" required>
                                                <option value="">Select Item</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm text-center qty-input" name="items[0][quantity]" min="1" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm text-end price-input" name="items[0][unit_price]" step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-end total-input" readonly tabindex="-1" value="0.00">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm" name="items[0][delivery_date]">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="items[0][remarks]">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-link text-danger remove-item p-0">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Subtotal:</th>
                                        <th id="subTotal" class="text-end">0.00</th>
                                        <th colspan="3"></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end text-danger">Tax (<span id="taxDisplay">0</span>%):</th>
                                        <th id="taxAmount" class="text-end text-danger">0.00</th>
                                        <th colspan="3"></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end h5 fw-bold">Grand Total:</th>
                                        <th id="grandTotal" class="text-end h5 fw-bold text-primary">0.00</th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mb-5 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                        <i class="fas fa-save me-2"></i> Save Sales Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        font-size: 0.875rem;
        min-height: 31px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
$(document).ready(function() {
    function initSelect2(element) {
        $(element).select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    initSelect2('.select2');
    initSelect2('.item-select');

    let itemIndex = 1;

    function updateSNo() {
        $('.row-sno').each(function(index) {
            $(this).text(index + 1);
        });
    }

    $('#addItem').click(function() {
        let firstRow = $('.item-row:first');
        let newRow = firstRow.clone();
        
        newRow.find('input').val('');
        newRow.find('.total-input').val('0.00');
        
        newRow.find('[name]').each(function() {
            let name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/\[\d+\]/, '[' + itemIndex + ']'));
            }
        });

        newRow.find('.select2-container').remove();
        newRow.find('.item-select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').show();
        
        $('#itemsBody').append(newRow);
        initSelect2(newRow.find('.item-select'));
        itemIndex++;
        updateSNo();
        
        let customerId = $('#customer_id').val();
        if (customerId) {
            updateRowItems(newRow.find('.item-select'), customerId);
        }
    });

    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            updateSNo();
            calculateTotals();
        } else {
            alert('At least one item is required.');
        }
    });

    $('#customer_id').change(function() {
        let customerId = $(this).val();
        let address = $(this).find(':selected').data('address');
        $('#customer_address').val(address || '');

        if (customerId) {
            $.get('{{ url("items/get-by-customer") }}/' + customerId, function(items) {
                $('.item-select').each(function() {
                    let select = $(this);
                    let currentValue = select.val();
                    select.empty().append('<option value="">Select Item</option>');
                    items.forEach(function(item) {
                        select.append(`<option value="${item.id}" data-price="${item.price}">${item.name}</option>`);
                    });
                    select.val(currentValue).trigger('change');
                });
            });
        } else {
            $('.item-select').empty().append('<option value="">Select Item</option>').trigger('change');
        }
    });

    function updateRowItems(select, customerId) {
        $.get('{{ url("items/get-by-customer") }}/' + customerId, function(items) {
            select.empty().append('<option value="">Select Item</option>');
            items.forEach(function(item) {
                select.append(`<option value="${item.id}" data-price="${item.price}">${item.name}</option>`);
            });
        });
    }

    $(document).on('change', '.item-select', function() {
        let price = $(this).find(':selected').data('price') || 0;
        let row = $(this).closest('.item-row');
        row.find('.price-input').val(price);
        calculateRowTotal(row);
    });

    $(document).on('input', '.qty-input, .price-input', function() {
        calculateRowTotal($(this).closest('.item-row'));
    });

    $('#tax_percent').change(function() {
        $('#taxDisplay').text($(this).val());
        calculateTotals();
    });

    function calculateRowTotal(row) {
        let qty = parseFloat(row.find('.qty-input').val()) || 0;
        let price = parseFloat(row.find('.price-input').val()) || 0;
        let total = qty * price;
        row.find('.total-input').val(total.toFixed(2));
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        $('.total-input').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });
        
        let taxPercent = parseFloat($('#tax_percent').val()) || 0;
        let taxAmount = (subtotal * taxPercent) / 100;
        let grandTotal = subtotal + taxAmount;

        $('#subTotal').text(subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#taxAmount').text(taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#grandTotal').text(grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }
});
</script>
@endpush
@endsection
