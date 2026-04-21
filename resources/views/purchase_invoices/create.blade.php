@extends('layouts.app')

@section('title', 'Create Purchase Invoice')

@section('content')
<div class="row">
    <div class="col-md-12">
        <form id="purchaseInvoiceForm" method="POST" action="{{ route('purchase_invoices.store') }}">
            @csrf
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0 text-white fw-bold"><i class="fas fa-file-invoice me-2"></i>New Purchase Invoice</h5>
                    <div class="fw-bold">No: {{ $invoiceNumber }}</div>
                    <input type="hidden" name="invoice_number" value="{{ $invoiceNumber }}">
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Posting Date <span class="text-danger">*</span></label>
                            <input type="date" name="posting_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Supplier Inv #</label>
                            <input type="text" name="supplier_invoice_number" class="form-control" placeholder="e.g. SUP-123">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Supplier (Party) <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select select2" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Agent</label>
                            <select name="agent_id" id="agent_id" class="form-select select2">
                                <option value="" data-comm="0">Direct Purchase</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" data-comm="{{ $agent->commission_percentage }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Comm %</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">%</span>
                                <input type="number" step="0.01" name="commission_percentage" id="commission_percentage" class="form-control" value="0.00" min="0" max="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Invoice Items</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="itemsTable">
                        <thead class="bg-label-secondary">
                            <tr>
                                <th style="width: 35%;">Item Description</th>
                                <th style="width: 15%;">Quantity</th>
                                <th style="width: 15%;">Unit Price</th>
                                <th style="width: 20%;">Total Price</th>
                                <th style="width: 10%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][purchase_item_id]" class="form-select item-select" required>
                                        <option value="">Select Item</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->purchase_price }}">{{ $item->name }} ({{ $item->unit }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[0][quantity]" class="form-control qty-input" value="1.00" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[0][unit_price]" class="form-control price-input" value="0.00" required>
                                </td>
                                <td class="text-end">
                                    <span class="row-total fw-bold">0.00</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-label-danger remove-row" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addRow">
                                        <i class="fas fa-plus me-1"></i> Add Another Item
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Totals & Summary -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <label class="form-label fw-bold">Notes / Description</label>
                            <textarea name="notes" class="form-control" rows="5" placeholder="Any additional details about this purchase..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="fw-bold">Gross Amount:</span>
                                <input type="hidden" id="gross_amount_val" name="gross_amount">
                                <span class="fw-bold fs-5 text-secondary" id="display_gross">0.00</span>
                            </div>
                            <div class="mb-3 border-bottom pb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-danger">Tax (<span id="tax_label">0</span>%):</span>
                                    <div class="w-50">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">%</span>
                                            <input type="number" step="0.01" name="tax_percentage" id="tax_percentage" class="form-control text-end" value="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end small text-muted">Amount: Rs. <span id="display_tax_amt">0.00</span></div>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2 text-info">
                                <span class="fw-bold">Agent Commission (<span id="comm_label">0</span>%):</span>
                                <span class="fw-bold fs-5" id="display_comm">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2">
                                <span class="fw-bold fs-4 text-primary">TOTAL PAYABLE:</span>
                                <span class="fw-bold fs-3 text-primary" id="display_net">0.00</span>
                            </div>
                            <div class="small text-muted text-end mt-1">
                                * Commission will be deducted during recovery.
                            </div>

                            <div class="mt-4 d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle me-1"></i> Post Purchase Invoice
                                </button>
                                <a href="{{ route('purchase_invoices.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let rowCount = 1;

    // Filter Items by Supplier
    $('select[name="supplier_id"]').on('change', function() {
        let supplierId = $(this).val();
        if (supplierId) {
            $.ajax({
                url: '{{ route("purchase_items.by_supplier", ":id") }}'.replace(':id', supplierId),
                type: 'GET',
                success: function(items) {
                    let options = '<option value="">Select Item</option>';
                    items.forEach(function(item) {
                        options += `<option value="${item.id}" data-price="${item.purchase_price}">${item.name} (${item.unit})</option>`;
                    });
                    
                    $('.item-select').each(function() {
                        $(this).html(options).val('').trigger('change.select2');
                        let row = $(this).closest('.item-row');
                        row.find('input').val('0.00');
                        row.find('.qty-input').val('1.00');
                        row.find('.row-total').text('0.00');
                    });
                    calculateGrandTotal();
                }
            });
        } else {
            $('.item-select').html('<option value="">Select Item</option>').val('').trigger('change.select2');
        }
    });

    // Handle Agent Selection for Commission %
    $('#agent_id').on('change', function() {
        let comm = $(this).find('option:selected').data('comm') || 0;
        $('#commission_percentage').val(parseFloat(comm).toFixed(2));
        $('#comm_label').text(parseFloat(comm).toFixed(2));
        calculateGrandTotal();
    });

    $('#commission_percentage').on('input', function() {
        $('#comm_label').text($(this).val() || 0);
        calculateGrandTotal();
    });

    $('#tax_percentage').on('input', function() {
        $('#tax_label').text($(this).val() || 0);
        calculateGrandTotal();
    });

    // Add row
    $('#addRow').on('click', function() {
        let newRow = $('.item-row').first().clone();
        newRow.find('input').val('0.00');
        newRow.find('.qty-input').val('1.00');
        newRow.find('.row-total').text('0.00');
        newRow.find('.item-select').val('');
        newRow.find('select, input').each(function() {
            let name = $(this).attr('name');
            $(this).attr('name', name.replace('[0]', '[' + rowCount + ']'));
        });
        newRow.find('.remove-row').removeAttr('disabled');
        $('#itemsTable tbody').append(newRow);
        rowCount++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateGrandTotal();
        }
    });

    // Handle Item Selection for Price
    $(document).on('change', '.item-select', function() {
        let price = $(this).find('option:selected').data('price') || 0;
        $(this).closest('.item-row').find('.price-input').val(parseFloat(price).toFixed(2));
        calculateRowTotal($(this).closest('.item-row'));
    });

    // Calculations
    $(document).on('input', '.qty-input, .price-input', function() {
        calculateRowTotal($(this).closest('.item-row'));
    });

    function calculateRowTotal(row) {
        let qty = parseFloat(row.find('.qty-input').val()) || 0;
        let price = parseFloat(row.find('.price-input').val()) || 0;
        let total = qty * price;
        row.find('.row-total').text(total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grossTotal = 0;
        $('.item-row').each(function() {
            let qty = parseFloat($(this).find('.qty-input').val()) || 0;
            let price = parseFloat($(this).find('.price-input').val()) || 0;
            grossTotal += (qty * price);
        });

        let taxPercent = parseFloat($('#tax_percentage').val()) || 0;
        let taxAmount = (grossTotal * taxPercent) / 100;
        
        let commPercent = parseFloat($('#commission_percentage').val()) || 0;
        let commAmount = (grossTotal * commPercent) / 100;
        
        // Net Payable is Gross + Tax.
        let netTotal = grossTotal + taxAmount;

        $('#display_gross').text(grossTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#display_tax_amt').text(taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#display_comm').text(commAmount.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#display_net').text(netTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#gross_amount_val').val(grossTotal.toFixed(2));
    }

    // Initial calc
    calculateGrandTotal();
});
</script>
@endpush
@endsection
