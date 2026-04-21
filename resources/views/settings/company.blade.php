@extends('layouts.app')

@section('title', 'Company Setup')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between p-3">
                <h5 class="mb-0 text-white"><i class="fas fa-building me-2"></i>Company Setup</h5>
                <small>Configure your brand identity</small>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-fill" id="companyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 fw-bold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab"><i class="fas fa-info-circle me-2"></i>General Info</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold" id="prefixes-tab" data-bs-toggle="tab" data-bs-target="#prefixes" type="button" role="tab"><i class="fas fa-tags me-2"></i>Document Prefixes</button>
                    </li>
                </ul>

                <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="tab-content p-4" id="companyTabsContent">
                        <!-- General Info Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row mb-4 text-center">
                                <div class="col-12">
                                    <div class="mb-3">
                                        @if(optional($setting)->logo_path)
                                            <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Company Logo" class="img-thumbnail shadow-sm mb-2" style="max-height: 100px;">
                                        @else
                                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 100px; height: 100px; border: 2px dashed #dee2e6;">
                                                <i class="fas fa-image fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <label for="logo" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-upload me-1"></i> Change Logo
                                        <input type="file" id="logo" name="logo" class="d-none" onchange="this.form.submit()">
                                    </label>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="name" class="form-label fw-bold small text-uppercase text-muted">Company Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', optional($setting)->name) }}" required maxlength="255">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold small text-uppercase text-muted">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', optional($setting)->email) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold small text-uppercase text-muted">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', optional($setting)->phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="website" class="form-label fw-bold small text-uppercase text-muted">Website</label>
                                    <input type="url" class="form-control" id="website" name="website" value="{{ old('website', optional($setting)->website) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="tax_number" class="form-label fw-bold small text-uppercase text-muted">Tax Number</label>
                                    <input type="text" class="form-control" id="tax_number" name="tax_number" value="{{ old('tax_number', optional($setting)->tax_number) }}">
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label fw-bold small text-uppercase text-muted">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address', optional($setting)->address) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label for="other_details" class="form-label fw-bold small text-uppercase text-muted">Header/Footer Text</label>
                                    <textarea class="form-control" id="other_details" name="other_details" rows="2">{{ old('other_details', optional($setting)->other_details) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Prefixes Tab -->
                        <div class="tab-pane fade" id="prefixes" role="tabpanel">
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <i class="fas fa-lightbulb me-2"></i> Settings here define the starting prefix for your documents. Numbers will follow automatically (e.g., <strong>INV</strong>-0001).
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase text-muted"><i class="fas fa-file-invoice me-1 text-primary"></i>Sales Bill Prefix</label>
                                    <input type="text" class="form-control" name="bill_prefix" value="{{ old('bill_prefix', optional($setting)->bill_prefix ?? 'BILL') }}" maxlength="10">
                                    <div class="form-text">Current: {{ optional($setting)->bill_prefix ?? 'BILL' }}-0001</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase text-muted"><i class="fas fa-truck me-1 text-primary"></i>Delivery Challan Prefix</label>
                                    <input type="text" class="form-control" name="challan_prefix" value="{{ old('challan_prefix', optional($setting)->challan_prefix ?? 'DC') }}" maxlength="10">
                                    <div class="form-text">Current: {{ optional($setting)->challan_prefix ?? 'DC' }}-0001</div>
                                </div>

                                <div class="col-md-12">
                                    <hr class="my-2">
                                    <h6 class="fw-bold mb-3 mt-2"><i class="fas fa-money-bill-transfer me-2 text-primary"></i>Financial Voucher Prefixes</h6>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Payment (PV)</label>
                                    <input type="text" class="form-control" name="pv_prefix" value="{{ old('pv_prefix', optional($setting)->pv_prefix ?? 'PV') }}" maxlength="10">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Receive (RV)</label>
                                    <input type="text" class="form-control" name="rv_prefix" value="{{ old('rv_prefix', optional($setting)->rv_prefix ?? 'RV') }}" maxlength="10">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Journal (JV)</label>
                                    <input type="text" class="form-control" name="jv_prefix" value="{{ old('jv_prefix', optional($setting)->jv_prefix ?? 'JV') }}" maxlength="10">
                                </div>

                                <div class="col-md-12 pt-3">
                                    <label for="currency_symbol" class="form-label fw-bold small text-uppercase text-muted">Global Currency Symbol</label>
                                    <div class="input-group" style="max-width: 250px;">
                                        <span class="input-group-text bg-light"><i class="fas fa-coins text-muted"></i></span>
                                        <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', optional($setting)->currency_symbol ?? 'Rs.') }}" maxlength="10">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-0 p-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="fas fa-save me-1"></i> Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
