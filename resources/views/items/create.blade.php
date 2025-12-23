@extends('layouts.app')

@section('title', 'Add Item')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Inventory /</span> Add Item
    </h4>
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to List
    </a>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-0">
                <h5 class="mb-0">Item Information</h5>
                <small class="text-muted float-end">Define new product or service</small>
            </div>
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('items.store') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Customer</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('customer_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="code" class="form-label">Item Code</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light"><i class="fas fa-barcode"></i></span>
                                <input type="text" class="form-control bg-light" id="code" name="code" value="{{ $nextCode }}" readonly>
                            </div>
                            <small class="text-muted">Auto-generated identifier</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="fas fa-box"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter item name..." required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="uom" class="form-label">Unit of Measure</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="fas fa-weight-hanging"></i></span>
                                <select class="form-select @error('uom') is-invalid @enderror" id="uom" name="uom" required>
                                    <option value="">Select UoM</option>
                                    @foreach($uomOptions as $option)
                                        <option value="{{ $option }}" {{ old('uom') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('uom')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Unit Price (₨)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" placeholder="0.00" required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-label-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Save Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-label-secondary {
        background-color: #f0f2f4;
        color: #697a8d;
    }
    .btn-label-secondary:hover {
        background-color: #e1e4e8;
    }
    .input-group-text {
        background-color: #fff;
    }
</style>
@endsection
