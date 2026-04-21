<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionEntry;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        $this->recordLedgerTransaction($payment);
    }

    public function updated(Payment $payment): void
    {
        $this->deleted($payment);
        $this->recordLedgerTransaction($payment);
    }

    public function deleted(Payment $payment): void
    {
        Transaction::where('reference_type', Payment::class)
            ->where('reference_id', $payment->id)
            ->delete();
    }

    private function recordLedgerTransaction(Payment $payment): void
    {
        $customerAccount = $payment->customer->account;
        
        // Receiver account: If payment_party is selected, find its account. Else use Cash in Hand.
        if ($payment->payment_party_id) {
            $receiverAccount = Account::where('payment_party_id', $payment->payment_party_id)->first();
        } else {
            $receiverAccount = Account::where('name', 'Cash in Hand')->first();
        }

        if ($customerAccount && $receiverAccount && $payment->amount > 0) {
            $transaction = Transaction::create([
                'date' => $payment->payment_date,
                'type' => $payment->payment_party_id ? 'third_party_payment' : 'direct_payment',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'narration' => ($payment->payment_party_id ? "Third-Party Payment via " . $receiverAccount->name : "Direct Payment") . " for Bill: " . ($payment->bill->bill_number ?? "#".$payment->bill_id),
                'total_amount' => $payment->amount,
                'created_by' => auth()->id() ?? $payment->created_by,
            ]);

            // DR Receiver Account (Asset Increase / Debt decrease)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $receiverAccount->id,
                'debit' => $payment->amount,
                'credit' => 0
            ]);

            // CR Customer (Asset Decrease - Receivable decreases)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $customerAccount->id,
                'debit' => 0,
                'credit' => $payment->amount
            ]);
        }
    }
}
