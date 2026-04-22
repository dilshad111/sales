@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-edit me-2 text-warning"></i>Edit Customer: {{ $customer->name }}</h1>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('customers.update', $customer) }}">
                @csrf
                @method('PUT')
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold"><i class="fas fa-user me-2 text-muted"></i>Customer Name</label>
                                <input type="text" class="form-control form-control-lg custom-input shadow-none" id="name" name="name" value="{{ old('name', $customer->name) }}" placeholder="Enter full name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold"><i class="fas fa-phone me-2 text-muted"></i>Phone Number</label>
                                <input type="text" class="form-control form-control-lg custom-input shadow-none" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="+92 3XX XXXXXXX" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold"><i class="fas fa-envelope me-2 text-muted"></i>Email Address</label>
                                <input type="email" class="form-control form-control-lg custom-input shadow-none" id="email" name="email" value="{{ old('email', $customer->email) }}" placeholder="example@domain.com" required>
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold"><i class="fas fa-tag me-2 text-muted"></i>Customer Type</label>
                                <select class="form-select form-select-lg custom-input shadow-none" id="type" name="type" required>
                                    <option value="Un-Official" {{ old('type', $customer->type) == 'Un-Official' ? 'selected' : '' }}>Un-Official</option>
                                    <option value="Official" {{ old('type', $customer->type) == 'Official' ? 'selected' : '' }}>Official</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold"><i class="fas fa-map-marker-alt me-2 text-muted"></i>Business Address</label>
                                <textarea class="form-control custom-input shadow-none" id="address" name="address" rows="3" placeholder="Enter complete office or business address" required>{{ old('address', $customer->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 font-weight-bold text-primary">Financial & Status Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label for="opening_balance" class="form-label fw-semibold"><i class="fas fa-balance-scale me-2 text-muted"></i>Opening Balance (₨)</label>
                                <input type="number" step="0.01" class="form-control form-control-lg custom-input shadow-none" id="opening_balance" name="opening_balance" value="{{ old('opening_balance', $customer->opening_balance) }}" placeholder="0.00">
                                <small class="text-muted mt-1 d-block">Positive = Debit, Negative = Credit</small>
                            </div>
                            <div class="col-md-4">
                                <label for="excess_qty_percent" class="form-label fw-semibold"><i class="fas fa-percent me-2 text-muted"></i>Excess Qty Allowance (%)</label>
                                <input type="number" step="0.01" class="form-control form-control-lg custom-input shadow-none" id="excess_qty_percent" name="excess_qty_percent" value="{{ old('excess_qty_percent', $customer->excess_qty_percent) }}" placeholder="0.00" min="0" max="100">
                                <small class="text-muted mt-1 d-block">Extra margin allowed for DC creation</small>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label fw-semibold"><i class="fas fa-toggle-on me-2 text-muted"></i>Account Status</label>
                                <select class="form-select form-select-lg custom-input shadow-none" id="status" name="status" required>
                                    <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mb-5">
                    <button type="submit" class="btn btn-warning btn-lg px-5 shadow-sm">
                        <i class="fas fa-save me-2"></i>Update Customer
                    </button>
                    <a href="{{ route('customers.index') }}" class="btn btn-light btn-lg px-4 ms-2 shadow-sm border">
                        <i class="fas fa-times me-2 text-danger"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-input {
        border: 1px solid #d9dee3;
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        background-color: #fcfcfd;
    }
    .custom-input:focus {
        border-color: #696cff !important;
        background-color: #fff;
        box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4) !important;
    }
    .form-label {
        color: #566a7f;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card {
        border-radius: 0.75rem;
    }
    .card-header {
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }
</style>
@endsection
