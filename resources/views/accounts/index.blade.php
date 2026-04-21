@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('content')
<style>
    .coa-tree {
        max-width: 1100px;
        margin: 0 auto;
    }
    .hover-bg:hover {
        background-color: rgba(67, 89, 113, 0.04);
    }
    .transition-all {
        transition: all 0.2s ease-in-out;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .font-13 { font-size: 13px; }
    .font-14 { font-size: 14px; }
    .font-11 { font-size: 11px; }
    .font-10 { font-size: 10px; }
    .sm-text { font-size: 0.75rem; }
    
    .toggler i.fa-chevron-right {
        transform: rotate(0deg);
    }
    .toggler.expanded i.fa-chevron-right {
        transform: rotate(90deg);
    }
    
    .children-container {
        border-left: 1px dashed #d9dee3;
        padding-left: 1rem;
    }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .btn-icon:hover {
        background-color: rgba(67, 89, 113, 0.1);
    }
    
    .tree-header {
        background: #f5f5f9;
        border-radius: 8px 8px 0 0;
        font-weight: 700;
        color: #566a7f;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
        <div>
            <h5 class="mb-0 text-primary font-weight-bold">
                <i class="fas fa-sitemap me-2"></i>Chart of Accounts
            </h5>
            <small class="text-muted">Hierarchical financial structure</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="expandAll()" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-none">
                <i class="fas fa-expand-arrows-alt me-1"></i>Expand All
            </button>
            <button onclick="collapseAll()" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-none">
                <i class="fas fa-compress-arrows-alt me-1"></i>Collapse All
            </button>
            <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-none">
                <i class="fas fa-plus me-1"></i>Add Account
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="tree-header d-flex justify-content-between align-items-center py-2 px-3 border-bottom font-11">
            <div class="flex-grow-1">Account Name & Code</div>
            <div class="d-flex gap-4">
                <div style="width: 80px;">Type</div>
                <div style="width: 150px;" class="text-end">Balance</div>
                <div style="width: 120px;" class="text-center">Action</div>
            </div>
        </div>
        
        <div class="coa-tree p-3">
            @include('accounts._tree_node', ['accounts' => $roots, 'level' => 0])
        </div>
    </div>
</div>

<script>
    function toggleAccount(el) {
        const container = el.closest('.account-item').querySelector('.children-container');
        if (container) {
            container.classList.toggle('d-none');
            el.classList.toggle('expanded');
        }
    }

    function expandAll() {
        document.querySelectorAll('.children-container').forEach(c => c.classList.remove('d-none'));
        document.querySelectorAll('.toggler').forEach(t => t.classList.add('expanded'));
    }

    function collapseAll() {
        document.querySelectorAll('.children-container').forEach(c => c.classList.add('d-none'));
        document.querySelectorAll('.toggler').forEach(t => t.classList.remove('expanded'));
    }
</script>
@endsection
