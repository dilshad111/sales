@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<h1><i class="fas fa-building me-2"></i>Company Settings</h1>
<form method="POST" action="{{ route('settings.company.update') }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label"><i class="fas fa-signature me-1"></i>Company Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', optional($setting)->name) }}" required maxlength="255">
    </div>

    <div class="mb-3">
        <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Company Address</label>
        <textarea class="form-control" id="address" name="address" rows="3" required maxlength="500">{{ old('address', optional($setting)->address) }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</form>
@endsection
