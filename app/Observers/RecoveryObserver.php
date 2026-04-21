<?php

namespace App\Observers;

use App\Models\Recovery;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionEntry;

class RecoveryObserver
{
    public function created(Recovery $recovery): void
    {
        $this->recordLedgerTransaction($recovery);
    }

    public function updated(Recovery $recovery): void
    {
        $this->deleted($recovery);
        $this->recordLedgerTransaction($recovery);
    }

    public function deleted(Recovery $recovery): void
    {
        Transaction::where('reference_type', Recovery::class)
            ->where('reference_id', $recovery->id)
            ->delete();
    }

    private function recordLedgerTransaction(Recovery $recovery): void
    {
        $invoice = $recovery->invoice;
        $agentAccount = Account::where('agent_id', $recovery->agent_id)->first();
        $supplierAccount = Account::where('supplier_id', $invoice->supplier_id)->first();
        $commAccount = Account::where('name', 'Agent Commission Expense')->first();
        $directorAccount = $recovery->directorAccount;

        if ($agentAccount && $supplierAccount && $commAccount && $directorAccount && $recovery->amount > 0) {
            $transaction = Transaction::create([
                'date' => $recovery->date,
                'type' => 'recovery',
                'reference_type' => Recovery::class,
                'reference_id' => $recovery->id,
                'narration' => "Recovery Rec #{$recovery->recovery_number} against Inv {$invoice->invoice_number}",
                'total_amount' => $recovery->amount,
                'created_by' => auth()->id() ?? $recovery->created_by,
            ]);

            // 1. DR Agent (Collected cash, agent owes us)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $agentAccount->id,
                'debit' => $recovery->amount,
                'credit' => 0
            ]);

            // 2. CR Supplier (Applying recovery against supplier debt)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $supplierAccount->id,
                'debit' => 0,
                'credit' => $recovery->amount
            ]);

            // 3. DR Commission Expense
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $commAccount->id,
                'debit' => $recovery->commission_deducted,
                'credit' => 0
            ]);

            // 4. CR Agent (Agent takes their cut from what they owe us)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $agentAccount->id,
                'debit' => 0,
                'credit' => $recovery->commission_deducted
            ]);

            // 5. CR Director Account (Net Profit recognized for director)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $directorAccount->id,
                'debit' => 0,
                'credit' => $recovery->net_amount_transfered
            ]);

            // 6. CR Agent (Direct payoff of what remains of recovery to company/director)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $agentAccount->id,
                'debit' => 0,
                'credit' => $recovery->net_amount_transfered
            ]);
        }
    }
}
