@extends('layouts.app')

@section('title', 'Item Details')

@section('content')
<h1><i class="fas fa-box me-2"></i>Item Details</h1>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $item->name }}</h5>
        <p><i class="fas fa-hashtag me-1"></i><strong>Code:</strong> <code>{{ $item->code }}</code></p>
        <p><i class="fas fa-user me-1"></i><strong>Customer:</strong> {{ $item->customer->name }}</p>
        <p><i class="fas fa-balance-scale me-1"></i><strong>UoM:</strong> {{ $item->uom }}</p>
        <p><i class="fas fa-rupee-sign me-1"></i><strong>Price:</strong> ₨{{ number_format($item->price, 2) }}</p>
    </div>
</div>
<a href="{{ route('items.edit', $item) }}" class="btn btn-warning mt-3"><i class="fas fa-edit me-1"></i>Edit</a>
<a href="{{ route('items.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left me-1"></i>Back</a>
@endsection
