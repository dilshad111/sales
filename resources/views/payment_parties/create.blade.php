@extends('layouts.app')

@section('title', 'Add Payment Party')

@section('content')
<h1><i class="fas fa-plus-circle me-2"></i>Add Payment Party</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('payment_parties.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label"><i class="fas fa-user-tag me-1"></i>Party Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label"><i class="fas fa-toggle-on me-1"></i>Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
            <a href="{{ route('payment_parties.index') }}" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
        </form>
    </div>
</div>
@endsection
