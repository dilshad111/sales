@extends('layouts.app')

@section('title', 'Payment Parties')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1><i class="fas fa-hand-holding-usd me-2"></i>Payment Parties</h1>
    <a href="{{ route('payment_parties.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Payment Party</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                    <th><i class="fas fa-user-tag me-1"></i>Name</th>
                    <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parties as $party)
                <tr>
                    <td>{{ $party->id }}</td>
                    <td>{{ $party->name }}</td>
                    <td>
                        <span class="badge {{ $party->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($party->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('payment_parties.edit', $party) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('payment_parties.destroy', $party) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
