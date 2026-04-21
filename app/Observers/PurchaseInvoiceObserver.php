<?php

namespace App\Observers;

use App\Models\PurchaseInvoice;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionEntry;

class PurchaseInvoiceObserver
{
    public function created(PurchaseInvoice $invoice): void
    {
        $this->recordLedgerTransaction($invoice);
    }

    public function updated(PurchaseInvoice $invoice): void
    {
        $this->deleted($invoice);
        $this->recordLedgerTransaction($invoice);
    }

    public function deleted(PurchaseInvoice $invoice): void
    {
        Transaction::where('reference_type', PurchaseInvoice::class)
            ->where('reference_id', $invoice->id)
            ->delete();
    }

    private function recordLedgerTransaction(PurchaseInvoice $invoice): void
    {
        $supplierAccount = Account::where('supplier_id', $invoice->supplier_id)->first();
        $purchaseAccount = Account::where('name', 'Purchase Account')->first();

        if ($supplierAccount && $purchaseAccount && $invoice->net_amount > 0) {
            $transaction = Transaction::create([
                'date' => $invoice->date,
                'type' => 'purchase_invoice',
                'reference_type' => PurchaseInvoice::class,
                'reference_id' => $invoice->id,
                'narration' => "Purchase Invoice: " . $invoice->invoice_number . " (Supplier Inv: " . ($invoice->supplier_invoice_number ?? 'N/A') . ") from " . $invoice->supplier->name,
                'total_amount' => $invoice->net_amount,
                'created_by' => auth()->id() ?? $invoice->created_by,
            ]);

            // DR Purchase Account (Expense + Tax portion - or just total value)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $purchaseAccount->id,
                'debit' => $invoice->net_amount,
                'credit' => 0
            ]);

            // CR Supplier (Liability Increase - The full amount company owes supplier)
            TransactionEntry::create([
                'transaction_id' => $transaction->id,
                'account_id' => $supplierAccount->id,
                'debit' => 0,
                'credit' => $invoice->net_amount
            ]);
        }
    }
}
