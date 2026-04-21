<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\FinancialYear;

class TransactionObserver
{
    /**
     * Handle the Transaction "creating" event.
     */
    public function creating(Transaction $transaction): void
    {
        // 1. Automatically detect financial year based on transaction date
        if (!$transaction->financial_year_id) {
            $fy = FinancialYear::forDate($transaction->date);
            
            if (!$fy) {
                throw new \Exception("Transaction date '{$transaction->date->format('Y-m-d')}' does not fall within any defined Financial Year.");
            }
            
            $transaction->financial_year_id = $fy->id;
        } else {
            $fy = FinancialYear::find($transaction->financial_year_id);
        }

        // 2. Prevent adding transactions if financial year is closed
        if ($fy && $fy->is_closed) {
            throw new \Exception("Cannot add transaction to a closed Financial Year: {$fy->name}.");
        }
    }

    /**
     * Handle the Transaction "updating" event.
     */
    public function updating(Transaction $transaction): void
    {
        $fy = FinancialYear::find($transaction->financial_year_id);
        
        // Prevent editing if FY is closed
        if ($fy && $fy->is_closed) {
            throw new \Exception("Cannot update transaction in a closed Financial Year: {$fy->name}.");
        }
        
        // If date is changed, re-verify FY
        if ($transaction->isDirty('date')) {
            $newFy = FinancialYear::forDate($transaction->date);
            if (!$newFy) {
                throw new \Exception("New date does not fall within any defined Financial Year.");
            }
            if ($newFy->is_closed) {
                throw new \Exception("The new date falls into a closed Financial Year.");
            }
            $transaction->financial_year_id = $newFy->id;
        }
    }

    /**
     * Handle the Transaction "deleting" event.
     */
    public function deleting(Transaction $transaction): void
    {
        $fy = FinancialYear::find($transaction->financial_year_id);
        if ($fy && $fy->is_closed) {
            throw new \Exception("Cannot delete transaction from a closed Financial Year.");
        }
    }
}
