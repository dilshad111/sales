@extends('layouts.app')

@section('title', 'Edit Payment Party')

@section('content')
<h1><i class="fas fa-edit me-2"></i>Edit Payment Party</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('payment_parties.update', $payment_party) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label"><i class="fas fa-user-tag me-1"></i>Party Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $payment_party->name }}" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label"><i class="fas fa-toggle-on me-1"></i>Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" {{ $payment_party->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $payment_party->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
            <a href="{{ route('payment_parties.index') }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
        </form>
    </div>
</div>
@endsection
