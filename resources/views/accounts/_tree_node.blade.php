@foreach($accounts as $account)
<div class="account-item {{ $account->is_group ? 'is-group' : 'is-ledger' }}" data-id="{{ $account->id }}">
    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light hover-bg px-2 rounded transition-all">
        <div class="d-flex align-items-center flex-grow-1">
            <div class="tree-line-wrapper me-2 text-muted">
                @if($level > 0)
                    @for($i = 0; $i < $level; $i++)
                        <span class="tree-line"></span>
                    @endfor
                @endif
            </div>

            <div class="toggler me-2 cursor-pointer {{ $account->children->isEmpty() ? 'invisible' : '' }}" onclick="toggleAccount(this)">
                <i class="fas fa-chevron-right text-muted sm-text transition-all"></i>
            </div>

            <div class="account-icon me-2 d-flex align-items-center">
                @if($account->is_group)
                    <i class="fas fa-folder text-warning shadow-sm"></i>
                    <a href="{{ route('accounts.create', ['parent_id' => $account->id]) }}" class="ms-1 text-success sm-text" title="Add Sub-account">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                @else
                    <i class="fas fa-file-invoice-dollar text-primary shadow-sm"></i>
                @endif
            </div>

            <div class="account-info">
                <span class="fw-bold {{ $account->is_group ? 'text-dark font-14' : 'text-secondary font-13' }}">
                    {{ $account->name }}
                </span>
                <span class="badge bg-light text-muted font-10 border ms-2 px-2">{{ $account->code }}</span>
                @if($account->category !== 'general')
                    <span class="badge bg-label-secondary font-10 ms-1">{{ ucfirst($account->category) }}</span>
                @endif
            </div>
        </div>

        <div class="account-actions d-flex align-items-center gap-4">
            <div class="type-indicator font-11 text-uppercase text-muted fw-bold" style="width: 80px;">
                {{ $account->type }}
            </div>
            
            <div class="balance-indicator text-end {{ $account->balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold font-13" style="width: 150px;">
                Rs. {{ number_format(abs($account->balance), 2) }} {{ $account->balance >= 0 ? 'DR' : 'CR' }}
            </div>

            <div class="actions d-flex gap-1" style="width: 120px;">
                @if($account->is_group)
                    <a href="{{ route('accounts.create', ['parent_id' => $account->id]) }}" class="btn btn-sm btn-icon border-0 text-success" title="Add Sub-account">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                @endif
                @if(!$account->is_group)
                    <a href="{{ route('ledger.show', $account) }}" class="btn btn-sm btn-icon border-0 text-info" title="Ledger"><i class="fas fa-book"></i></a>
                @endif
                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-icon border-0 text-primary" title="Edit"><i class="fas fa-edit"></i></a>
                @if($account->children->isEmpty() && !$account->entries()->exists())
                <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-icon border-0 text-danger" onclick="return confirm('Are you sure you want to delete this record?')"><i class="fas fa-trash"></i></button>
                </form>
                @endif
            </div>
        </div>
    </div>

    @if(!$account->children->isEmpty())
        <div class="children-container d-none ms-3 border-start pl-3">
            @include('accounts._tree_node', ['accounts' => $account->children, 'level' => $level + 1])
        </div>
    @endif
</div>
@endforeach
