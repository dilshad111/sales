<?php

namespace App\Observers;

use App\Models\CommissionPayment;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionEntry;

class CommissionPaymentObserver
{
    public function created(CommissionPayment $payment): void
    {
        $this->recordLedgerTransaction($payment);
    }

    public function updated(CommissionPayment $payment): void
    {
        $this->deleted($payment);
        $this->recordLedgerTransaction($payment);
    }

    public function deleted(CommissionPayment $payment): void
    {
        Transaction::where('reference_type', CommissionPayment::class)
            ->where('reference_id', $payment->id)
            ->delete();
    }

    private function recordLedgerTransaction(CommissionPayment $payment): void
    {
        $agentAccount = Account::where('user_id', $payment->user_id)->first();
        $cashAccount = Account::where('name', 'Cash in Hand')->first();

        if ($agentAccount && $cashAccount && $payment->amount > 0) {
            $transaction = Transaction::create([
                'date' => $payment->payment_date,
                'type' => 'bill_payment',
                'reference_type' => CommissionPayment::class,
                'reference_id' => $payment->id,
                'narration' => "Commission Payment: " . ($payment->reference ?: "To " . $payment->user->name),
                'total_amount' => $payment->amount,
                'created_by' => auth()->id() ?? $payment->created_by,
            ]);

            // DR Agent (Liability Decrease)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $agentAccount->id,
                'debit' => $payment->amount,
                'credit' => 0
            ]);

            // CR Cash (Asset Decrease)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $payment->amount
            ]);
        }
    }
}
