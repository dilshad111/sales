<?php

namespace App\Observers;

use App\Models\Commission;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionEntry;

class CommissionObserver
{
    public function created(Commission $commission): void
    {
        $this->recordLedgerTransaction($commission);
    }

    public function updated(Commission $commission): void
    {
        $this->deleted($commission);
        $this->recordLedgerTransaction($commission);
    }

    public function deleted(Commission $commission): void
    {
        Transaction::where('reference_type', Commission::class)
            ->where('reference_id', $commission->id)
            ->delete();
    }

    private function recordLedgerTransaction(Commission $commission): void
    {
        $agentAccount = Account::where('user_id', $commission->user_id)->first();
        $expenseAccount = Account::where('name', 'General Expenses')->first();

        if ($agentAccount && $expenseAccount && $commission->amount > 0) {
            $transaction = Transaction::create([
                'date' => $commission->commission_date,
                'type' => 'multi_party_adjustment',
                'reference_type' => Commission::class,
                'reference_id' => $commission->id,
                'narration' => "Agent Commission: " . ($commission->reference ?? "For " . $commission->customer->name),
                'total_amount' => $commission->amount,
                'created_by' => auth()->id() ?? $commission->created_by,
            ]);

            // DR Expense (Company pays out)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $expenseAccount->id,
                'debit' => $commission->amount,
                'credit' => 0
            ]);

            // CR Agent (Liability: we owe Salman)
            // Note: Debit is increase for assets, Credit is increase for liabilities.
            // Since Agent's ledger shows what WE owe them (Net Balance):
            // Credit increases the balance we owe.
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $agentAccount->id,
                'debit' => 0,
                'credit' => $commission->amount
            ]);
        }
    }
}
