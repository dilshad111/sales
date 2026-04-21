<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\TransactionEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::where('status', 'active')->orderBy('name')->get();
        return view('ledger.index', compact('accounts'));
    }

    public function show(Request $request, Account $account)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();

        $query = TransactionEntry::with('transaction.entries.account')
            ->where('account_id', $account->id);

        if ($startDate) {
            $query->whereHas('transaction', function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }
        
        if ($endDate) {
            $query->whereHas('transaction', function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }

        $entries = $query->join('transactions', 'transaction_entries.transaction_id', '=', 'transactions.id')
            ->orderBy('transactions.date', 'asc')
            ->orderBy('transactions.id', 'asc')
            ->select('transaction_entries.*')
            ->get();

        // Calculate Opening Balance for the period if startDate is provided
        $openingBalance = $account->opening_balance;
        if ($startDate) {
            $preEntries = TransactionEntry::where('account_id', $account->id)
                ->whereHas('transaction', function ($q) use ($startDate) {
                    $q->where('date', '<', $startDate);
                })
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();
            
            $openingBalance += ($preEntries->total_debit ?? 0) - ($preEntries->total_credit ?? 0);
        }

        return view('ledger.show', compact('account', 'entries', 'openingBalance', 'startDate', 'endDate'));
    }

    public function pdf(Request $request, Account $account)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();

        // Re-use logic for PDF
        $query = TransactionEntry::with('transaction.entries.account')
            ->where('account_id', $account->id);

        if ($startDate) {
            $query->whereHas('transaction', function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }
        
        if ($endDate) {
            $query->whereHas('transaction', function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }

        $entries = $query->join('transactions', 'transaction_entries.transaction_id', '=', 'transactions.id')
            ->orderBy('transactions.date', 'asc')
            ->orderBy('transactions.id', 'asc')
            ->select('transaction_entries.*')
            ->get();

        $openingBalance = $account->opening_balance;
        if ($startDate) {
            $preEntries = TransactionEntry::where('account_id', $account->id)
                ->whereHas('transaction', function ($q) use ($startDate) {
                    $q->where('date', '<', $startDate);
                })
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();
            $openingBalance += ($preEntries->total_debit ?? 0) - ($preEntries->total_credit ?? 0);
        }

        $pdf = Pdf::loadView('ledger.pdf', compact('account', 'entries', 'openingBalance', 'startDate', 'endDate'));
        if ($request->has('print')) {
            return $pdf->stream("ledger_{$account->name}_{$endDate->format('d_m_Y')}.pdf");
        }
        return $pdf->download("ledger_{$account->name}_{$endDate->format('d_m_Y')}.pdf");
    }
}
