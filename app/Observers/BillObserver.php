<?php

namespace App\Observers;

use App\Models\Bill;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionEntry;

class BillObserver
{
    public function created(Bill $bill): void
    {
        $this->recordLedgerTransaction($bill);
    }

    public function updated(Bill $bill): void
    {
        // Delete old transaction and record new one
        $this->deleted($bill);
        $this->recordLedgerTransaction($bill);
    }

    public function deleted(Bill $bill): void
    {
        Transaction::where('reference_type', Bill::class)
            ->where('reference_id', $bill->id)
            ->delete();
    }

    private function recordLedgerTransaction(Bill $bill): void
    {
        $customerAccount = $bill->customer->account;
        $salesAccount = Account::where('name', 'Sales Revenue')->first();

        if ($customerAccount && $salesAccount && $bill->total > 0) {
            $transaction = Transaction::create([
                'date' => $bill->bill_date,
                'type' => 'bill_payment',
                'reference_type' => Bill::class,
                'reference_id' => $bill->id,
                'narration' => "Sales Bill: " . $bill->bill_number . " for " . $bill->customer->name,
                'total_amount' => $bill->total,
                'created_by' => auth()->id() ?? $bill->created_by,
            ]);

            // DR Customer (Asset Increase)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $customerAccount->id,
                'debit' => $bill->total,
                'credit' => 0
            ]);

            // CR Sales Revenue (Income Increase)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $bill->total
            ]);
        }
    }
}
