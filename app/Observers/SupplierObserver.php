<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Supplier;

class SupplierObserver
{
    public function created(Supplier $supplier): void
    {
        $parent = Account::where('name', 'Trade Creditors')->first() ?? Account::where('name', 'Accounts Payable')->first();

        Account::create([
            'name' => $supplier->name,
            'category' => 'supplier',
            'type' => 'Liability',
            'parent_id' => $parent?->id,
            'is_group' => false,
            'supplier_id' => $supplier->id,
            'phone' => $supplier->phone,
            'email' => $supplier->email,
            'address' => $supplier->address,
            'status' => $supplier->status ?? 'active',
        ]);
    }

    public function updated(Supplier $supplier): void
    {
        $account = Account::where('supplier_id', $supplier->id)->first();
        if ($account) {
            $account->update([
                'name' => $supplier->name,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'address' => $supplier->address,
                'status' => $supplier->status ?? 'active',
            ]);
        }
    }
}
