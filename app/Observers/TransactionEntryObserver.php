<?php

namespace App\Observers;

use App\Models\TransactionEntry;
use Illuminate\Support\Facades\Log;

class TransactionEntryObserver
{
    /**
     * Handle the TransactionEntry "created" event.
     */
    public function created(TransactionEntry $entry): void
    {
        $account = $entry->account;
        if (!$account || !$account->payment_party_id) {
            return;
        }

        $paymentParty = $account->paymentParty;
        if (!$paymentParty || !$paymentParty->account) {
            return;
        }

        $mainPartyAccount = $paymentParty->account;

        // Skip if we are posting to the main party account itself locally
        if ((int)$entry->account_id == (int)$mainPartyAccount->id) {
            return;
        }

        // We need to create two entries in the same transaction:
        // 1. A mirror entry in the Main Party's account
        // 2. A contra entry in the Sub-Party's account to maintain transaction balance
        
        // This keeps the Sub-Party ledger updated for history (DR and CR balanced)
        // while the liability/asset is officially transferred to the Main Party.

        TransactionEntry::withoutEvents(function () use ($entry, $mainPartyAccount) {
            // 1. Mirror entry to Main Party
            TransactionEntry::create([
                'transaction_id' => $entry->transaction_id,
                'account_id' => $mainPartyAccount->id,
                'debit' => $entry->debit,
                'credit' => $entry->credit,
            ]);

            // 2. Contra entry to Sub-Party
            TransactionEntry::create([
                'transaction_id' => $entry->transaction_id,
                'account_id' => $entry->account_id,
                'debit' => $entry->credit, // Swap debit and credit
                'credit' => $entry->debit,
            ]);
        });
    }
}
