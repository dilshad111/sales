@extends('layouts.app')

@section('title', 'Edit Purchase Item')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-warning"></i>Edit Purchase Item: {{ $purchaseItem->name }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('purchase_items.update', $purchaseItem) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted text-uppercase">Supplier (Link to)</label>
                                <select name="supplier_id" class="form-select select2 @error('supplier_id') is-invalid @enderror">
                                    <option value="">Select Supplier (Global item if empty)</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseItem->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted text-uppercase">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $purchaseItem->name) }}" placeholder="e.g. Raw Material, Office Supplies" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Unit of Measure (UOM)</label>
                                <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $purchaseItem->unit) }}" placeholder="e.g. kg, meter, pcs">
                                @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Standard Purchase Price</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-muted">{{ $companySetting->currency_symbol ?? 'Rs.' }}</span>
                                    <input type="number" step="0.01" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price', $purchaseItem->purchase_price) }}" placeholder="0.00">
                                </div>
                                @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ old('status', $purchaseItem->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $purchaseItem->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('purchase_items.index') }}" class="btn btn-light px-4 fw-bold">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="fas fa-save me-1"></i> Update Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('.card-body')
        });
    });
</script>
@endpush
