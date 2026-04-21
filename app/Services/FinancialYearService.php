<?php

namespace App\Services;

use App\Models\FinancialYear;
use App\Models\Transaction;
use App\Models\TransactionEntry;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialYearService
{
    /**
     * Close a financial year and carry over balances.
     */
    public function closeYear(FinancialYear $fy, $closedBy)
    {
        if ($fy->is_closed) {
            throw new \Exception("Financial year is already closed.");
        }

        return DB::transaction(function () use ($fy, $closedBy) {
            // 1. Calculate Net Profit/Loss (Income - Expense)
            $income = $this->getCategoryBalance($fy, 'income');
            $expense = $this->getCategoryBalance($fy, 'expense');
            $netProfit = $income - $expense;

            // 2. Transfer Profit/Loss to Retained Earnings
            $retainedEarningsAccount = $this->getOrCreateRetainedEarningsAccount();
            
            if ($netProfit != 0) {
                $this->createClosingEntry($fy, $retainedEarningsAccount, $netProfit);
            }

            // 3. Mark FY as closed
            $fy->update([
                'is_closed' => true,
                'closed_at' => now(),
                'closed_by' => $closedBy
            ]);

            // 4. Generate Opening Balances for the NEXT year (if defined)
            $nextFy = FinancialYear::where('start_date', $fy->end_date->addDay())->first();
            if ($nextFy) {
                $this->generateOpeningBalances($fy, $nextFy);
            }

            return $fy;
        });
    }

    /**
     * Get net balance for a specific account category in a financial year.
     */
    protected function getCategoryBalance(FinancialYear $fy, $type)
    {
        return DB::table('transaction_entries')
            ->join('accounts', 'transaction_entries.account_id', '=', 'accounts.id')
            ->join('transactions', 'transaction_entries.transaction_id', '=', 'transactions.id')
            ->where('transactions.financial_year_id', $fy->id)
            ->where('accounts.type', $type)
            ->select(DB::raw('SUM(credit) - SUM(debit) as balance'))
            ->value('balance') ?: 0;
    }

    /**
     * Create a closing transaction entry for Retained Earnings.
     */
    protected function createClosingEntry(FinancialYear $fy, Account $account, $amount)
    {
        $transaction = Transaction::create([
            'financial_year_id' => $fy->id,
            'transaction_number' => 'CL-' . $fy->id . '-' . rand(100, 999),
            'date' => $fy->end_date,
            'type' => 'JV',
            'narration' => 'Financial Year Closing Entry: Net Profit/Loss transfer to Retained Earnings',
            'total_amount' => abs($amount)
        ]);

        TransactionEntry::create([
            'transaction_id' => $transaction->id,
            'account_id' => $account->id,
            'debit' => $amount < 0 ? abs($amount) : 0,
            'credit' => $amount > 0 ? $amount : 0,
        ]);
        
        // Note: In real double-entry, you'd also zero out the income/expense accounts.
        // For simplicity and audit tracking, we often just transfer the net.
    }

    /**
     * Generate opening entries in the new FY based on Asset/Liability/Equity balances.
     */
    protected function generateOpeningBalances(FinancialYear $oldFy, FinancialYear $newFy)
    {
        // Get all balances for Assets, Liabilities, and Equity
        $balances = DB::table('transaction_entries')
            ->join('accounts', 'transaction_entries.account_id', '=', 'accounts.id')
            ->join('transactions', 'transaction_entries.transaction_id', '=', 'transactions.id')
            ->where('transactions.financial_year_id', $oldFy->id)
            ->whereIn('accounts.type', ['asset', 'liability', 'equity'])
            ->select('accounts.id', 'accounts.name', DB::raw('SUM(debit) - SUM(credit) as balance'))
            ->groupBy('accounts.id', 'accounts.name')
            ->get();

        if ($balances->isEmpty()) return;

        $openingTransaction = Transaction::create([
            'financial_year_id' => $newFy->id,
            'transaction_number' => 'OP-' . $newFy->id,
            'date' => $newFy->start_date,
            'type' => 'JV',
            'narration' => 'Opening Balances from Financial Year ' . $oldFy->name,
            'total_amount' => $balances->sum(fn($b) => abs($b->balance))
        ]);

        foreach ($balances as $b) {
            if ($b->balance == 0) continue;

            TransactionEntry::create([
                'transaction_id' => $openingTransaction->id,
                'account_id' => $b->id,
                'debit' => $b->balance > 0 ? $b->balance : 0,
                'credit' => $b->balance < 0 ? abs($b->balance) : 0,
            ]);
        }
    }

    protected function getOrCreateRetainedEarningsAccount()
    {
        return Account::firstOrCreate(
            ['name' => 'Retained Earnings'],
            ['type' => 'equity', 'code' => 'RE-001']
        );
    }
}
