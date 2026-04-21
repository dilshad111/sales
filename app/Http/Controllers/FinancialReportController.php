<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\TransactionEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function trialBalance(Request $request)
    {
        $data = $this->buildTrialBalanceData($request);
        if ($request->has('pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.financial.trial_balance_pdf', $data);
            return $request->has('print') ? $pdf->stream('trial_balance.pdf') : $pdf->download('trial_balance.pdf');
        }
        if ($request->has('excel')) {
            return $this->exportTrialBalanceExcel($data);
        }
        return view('reports.financial.trial_balance', $data);
    }

    public function profitLoss(Request $request)
    {
        $data = $this->buildProfitLossData($request);
        if ($request->has('pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.financial.profit_loss_pdf', $data);
            return $request->has('print') ? $pdf->stream('profit_loss.pdf') : $pdf->download('profit_loss.pdf');
        }
        if ($request->has('excel')) {
            return $this->exportProfitLossExcel($data);
        }
        return view('reports.financial.profit_loss', $data);
    }

    public function balanceSheet(Request $request)
    {
        $data = $this->buildBalanceSheetData($request);
        if ($request->has('pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.financial.balance_sheet_pdf', $data);
            return $request->has('print') ? $pdf->stream('balance_sheet.pdf') : $pdf->download('balance_sheet.pdf');
        }
        if ($request->has('excel')) {
            return $this->exportBalanceSheetExcel($data);
        }
        return view('reports.financial.balance_sheet', $data);
    }

    private function buildTrialBalanceData(Request $request)
    {
        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $selectedYearId = $request->input('financial_year_id');
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        if ($selectedYearId) {
            $year = \App\Models\FinancialYear::find($selectedYearId);
            if ($year) $date = $year->end_date;
        }

        $accounts = Account::where('is_group', false)->where('status', 'active')->get()
            ->map(function($account) use ($date) {
                $balance = $account->getBalanceAtDate($date);
                return [
                    'account' => $account,
                    'debit' => $balance > 0 ? $balance : 0,
                    'credit' => $balance < 0 ? abs($balance) : 0,
                ];
            })->filter(fn($a) => $a['debit'] != 0 || $a['credit'] != 0);

        return compact('accounts', 'date', 'financialYears', 'selectedYearId');
    }

    private function buildProfitLossData(Request $request)
    {
        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $selectedYearId = $request->input('financial_year_id');
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();

        if ($selectedYearId) {
            $year = \App\Models\FinancialYear::find($selectedYearId);
            if ($year) {
                $startDate = $year->start_date;
                $endDate = $year->end_date;
            }
        }

        $incomeAccounts = $this->getAccountPeriodData('Income', $startDate, $endDate);
        $expenseAccounts = $this->getAccountPeriodData('Expense', $startDate, $endDate);
        $totalIncome = $incomeAccounts->sum('net_change');
        $totalExpense = $expenseAccounts->sum('net_change');
        $netProfit = abs($totalIncome) - $totalExpense;

        return compact('incomeAccounts', 'expenseAccounts', 'totalIncome', 'totalExpense', 'netProfit', 'startDate', 'endDate', 'financialYears', 'selectedYearId');
    }

    private function buildBalanceSheetData(Request $request)
    {
        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $selectedYearId = $request->input('financial_year_id');
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

        if ($selectedYearId) {
            $year = \App\Models\FinancialYear::find($selectedYearId);
            if ($year) $date = $year->end_date;
        }

        $assets = $this->getAccountBalanceData('Asset', $date);
        $liabilities = $this->getAccountBalanceData('Liability', $date);
        $equity = $this->getAccountBalanceData('Equity', $date);
        $profitData = $this->calculateProfitUpToDate($date);
        $retainedEarnings = $profitData['net_profit'];

        return compact('assets', 'liabilities', 'equity', 'retainedEarnings', 'date', 'financialYears', 'selectedYearId');
    }

    private function getAccountBalanceData($type, $date)
    {
        return Account::where('type', $type)
            ->where('is_group', false)
            ->get()
            ->map(function($account) use ($date) {
                $balance = $account->getBalanceAtDate($date);
                return [
                    'name' => $account->name,
                    'code' => $account->code,
                    'balance' => $balance
                ];
            })->filter(fn($a) => $a['balance'] != 0);
    }

    private function getAccountPeriodData($type, $startDate, $endDate)
    {
        return Account::where('type', $type)
            ->where('is_group', false)
            ->get()
            ->map(function($account) use ($startDate, $endDate) {
                $sums = $account->entries()
                    ->whereHas('transaction', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    })
                    ->selectRaw('SUM(debit) as debits, SUM(credit) as credits')
                    ->first();
                
                $netChange = ($sums->debits ?? 0) - ($sums->credits ?? 0);
                return (object)[
                    'name' => $account->name,
                    'code' => $account->code,
                    'net_change' => $netChange
                ];
            })->filter(fn($a) => $a->net_change != 0);
    }

    private function calculateProfitUpToDate($date)
    {
        $income = Account::where('type', 'Income')->where('is_group', false)->get()->sum(fn($a) => $a->getBalanceAtDate($date));
        $expense = Account::where('type', 'Expense')->where('is_group', false)->get()->sum(fn($a) => $a->getBalanceAtDate($date));
        
        return [
            'income' => abs($income),
            'expense' => $expense,
            'net_profit' => abs($income) - $expense
        ];
    }

    private function exportTrialBalanceExcel($data)
    {
        $headers = ['Account Code', 'Account Name', 'Debit', 'Credit'];
        return response()->streamDownload(function() use ($data, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data['accounts'] as $row) {
                fputcsv($file, [$row['account']->code, $row['account']->name, $row['debit'], $row['credit']]);
            }
            fclose($file);
        }, 'trial_balance_' . $data['date']->format('Ymd') . '.csv');
    }

    private function exportProfitLossExcel($data)
    {
        return response()->streamDownload(function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Income Statement (P&L)']);
            fputcsv($file, ['Period:', $data['startDate']->format('d/m/Y') . ' to ' . $data['endDate']->format('d/m/Y')]);
            fputcsv($file, []);
            fputcsv($file, ['INCOME']);
            foreach ($data['incomeAccounts'] as $row) fputcsv($file, [$row->name, abs($row->net_change)]);
            fputcsv($file, ['Total Income', abs($data['totalIncome'])]);
            fputcsv($file, []);
            fputcsv($file, ['EXPENSES']);
            foreach ($data['expenseAccounts'] as $row) fputcsv($file, [$row->name, $row->net_change]);
            fputcsv($file, ['Total Expenses', $data['totalExpense']]);
            fputcsv($file, []);
            fputcsv($file, ['NET PROFIT/LOSS', $data['netProfit']]);
            fclose($file);
        }, 'profit_loss_' . $data['endDate']->format('Ymd') . '.csv');
    }

    private function exportBalanceSheetExcel($data)
    {
        return response()->streamDownload(function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Balance Sheet as of ' . $data['date']->format('d/m/Y')]);
            fputcsv($file, []);
            fputcsv($file, ['ASSETS']);
            foreach ($data['assets'] as $row) fputcsv($file, [$row['name'], $row['balance']]);
            fputcsv($file, ['TOTAL ASSETS', collect($data['assets'])->sum('balance')]);
            fputcsv($file, []);
            fputcsv($file, ['LIABILITIES']);
            foreach ($data['liabilities'] as $row) fputcsv($file, [$row['name'], abs($row['balance'])]);
            fputcsv($file, ['TOTAL LIABILITIES', collect($data['liabilities'])->sum(fn($l) => abs($l['balance']))]);
            fputcsv($file, []);
            fputcsv($file, ['EQUITY']);
            foreach ($data['equity'] as $row) fputcsv($file, [$row['name'], abs($row['balance'])]);
            fputcsv($file, ['Retained Earnings', $data['retainedEarnings']]);
            fputcsv($file, ['TOTAL EQUITY', collect($data['equity'])->sum(fn($e) => abs($e['balance'])) + $data['retainedEarnings']]);
            fclose($file);
        }, 'balance_sheet_' . $data['date']->format('Ymd') . '.csv');
    }
}
