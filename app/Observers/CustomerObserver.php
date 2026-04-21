<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Customer;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        $parent = Account::where('name', 'Accounts Receivable')->first();
        
        Account::create([
            'name' => $customer->name,
            'category' => 'customer',
            'type' => 'Asset',
            'parent_id' => $parent?->id,
            'is_group' => false,
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'address' => $customer->address,
            'opening_balance' => $customer->opening_balance ?? 0,
            'status' => $customer->status ?? 'active',
        ]);
    }

    public function updated(Customer $customer): void
    {
        $account = $customer->account;
        if ($account) {
            $account->update([
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->address,
                'opening_balance' => $customer->opening_balance ?? 0,
                'status' => $customer->status ?? 'active',
            ]);
        }
    }

    public function deleted(Customer $customer): void
    {
        // Don't delete the account to maintain historical transactions, but we can de-link or mark inactive.
        $account = $customer->account;
        if ($account) {
            $account->update([
                'status' => 'inactive'
            ]);
        }
    }
}
