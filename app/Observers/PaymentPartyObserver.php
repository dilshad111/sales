<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\PaymentParty;

class PaymentPartyObserver
{
    public function created(PaymentParty $paymentParty): void
    {
        $parent = Account::where('name', 'Special Partners')->first();

        Account::create([
            'name' => $paymentParty->name,
            'category' => 'payment_party',
            'type' => 'Liability',
            'parent_id' => $parent?->id,
            'is_group' => false,
            'payment_party_id' => $paymentParty->id,
            'phone' => $paymentParty->phone,
            'email' => $paymentParty->email,
            'address' => $paymentParty->address,
            'opening_balance' => $paymentParty->opening_balance ?? 0,
            'status' => $paymentParty->status ?? 'active',
        ]);
    }

    public function updated(PaymentParty $paymentParty): void
    {
        $account = Account::where('payment_party_id', $paymentParty->id)->first();
        if ($account) {
            $account->update([
                'name' => $paymentParty->name,
                'phone' => $paymentParty->phone,
                'email' => $paymentParty->email,
                'address' => $paymentParty->address,
                'opening_balance' => $paymentParty->opening_balance ?? 0,
                'status' => $paymentParty->status ?? 'active',
            ]);
        }
    }

    public function deleted(PaymentParty $paymentParty): void
    {
        $account = Account::where('payment_party_id', $paymentParty->id)->first();
        if ($account) {
            $account->update([
                'status' => 'inactive'
            ]);
        }
    }
}
